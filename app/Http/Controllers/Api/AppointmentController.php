<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\MedicalRecord; // 👇 INI YANG KURANG TADI
use App\Models\Prescription;  // 👇 INI JUGA KURANG
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AppointmentController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'poli_id' => 'required',
            'tanggal' => 'required',
            'jam' => 'required',
        ]);

        // Pastikan user benar-benar login via sanctum
        if (!Auth::guard('sanctum')->check()) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $userId = Auth::guard('sanctum')->id();

        // Logika nomor antrean
        $countToday = Appointment::where('tanggal', $request->tanggal)->count();
        $queueNumber = 'A-' . ($countToday + 1);

        $appointment = Appointment::create([
            'user_id' => $userId,
            'poli_id' => $request->poli_id,
            'queue_number' => $queueNumber,
            'tanggal' => $request->tanggal,
            'jam' => $request->jam,
            'status' => 'confirmed'
        ]);

        // Load relasi user biar Flutter dapet nama pasiennya
        return response()->json([
            'success' => true,
            'message' => 'Booking sukses!',
            'data' => $appointment->load('user')
        ], 201);
    }
    
    // Tambahkan Request $request di dalam kurung ini
    public function getQueueStatus(Request $request)
    {
        $userId = $request->user()->id; // Sekarang $request tidak akan error lagi
        $today = date('M d, 2026');

        $myAppointment = Appointment::where('user_id', $userId)
            ->where('tanggal', $today)
            ->where('status', 'confirmed')
            ->first();

        $nowServing = Appointment::where('tanggal', $today)
            ->where('status', 'confirmed')
            ->min('queue_number');

        return response()->json([
            'my_queue' => $myAppointment ? $myAppointment->queue_number : 'A-0',
            'now_serving' => $nowServing ?? 'A-1',
        ]);
    }
    
    public function getHistory(Request $request)
    {
        $userId = $request->user()->id;

        // Ambil semua data appointment user ini, urutkan dari yang terbaru
        // Pastikan load relasi 'poli' dan 'user'
        $history = Appointment::with(['poli', 'user'])
            ->where('user_id', $userId)
            ->orderBy('tanggal', 'desc')
            ->orderBy('jam', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $history
        ], 200);
    }
    
    public function simulateExamination($id)
    {
        try {
            // 1. Cari data booking tiketnya
            $appointment = Appointment::findOrFail($id);

            // Cek kalau udah selesai, jangan diperiksa dua kali
            if ($appointment->status === 'completed') {
                return response()->json(['success' => false, 'message' => 'Pasien ini sudah diperiksa.'], 400);
            }

            // 2. Ubah status antrean jadi selesai
            $appointment->status = 'completed';
            $appointment->save();

            // 3. Bikin Rekam Medis Otomatis
            // Asumsi: Ambil ID dokter pertama dari poli tempat dia berobat (kalau ada)
            $doctorId = 1; // Default ID dokter kalau relasinya belum sempurna
            
            $record = MedicalRecord::create([
                'user_id' => $appointment->user_id,
                'appointment_id' => $appointment->id,
                'doctor_id' => $doctorId, 
                'diagnosis' => 'Simulasi: Infeksi Saluran Pernapasan Akut (ISPA)',
                'doctor_notes' => 'Tenggorokan pasien terlihat merah. Disarankan banyak minum air putih.',
                'treatment_plan' => 'Istirahat total 2 hari, hindari minuman dingin dan berminyak.',
            ]);

            $doctor = \App\Models\Doctor::find($doctorId) ?? \App\Models\Doctor::first();
            $consultationPrice = $doctor ? $doctor->price : 100000;

            // 4. Kasih Resep Obat Otomatis
            $prescriptionsData = [
                [
                    'medical_record_id' => $record->id,
                    'medicine_name' => 'Paracetamol 500mg',
                    'dosage' => '1 Tablet',
                    'rules' => '3x Sehari (Sesudah Makan)',
                    'price' => 15000,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'medical_record_id' => $record->id,
                    'medicine_name' => 'Amoxicillin 500mg',
                    'dosage' => '1 Kapsul',
                    'rules' => '3x Sehari (Habiskan)',
                    'price' => 35000,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            ];
            Prescription::insert($prescriptionsData);

            $totalMedicines = collect($prescriptionsData)->sum('price');
            $grandTotal = $consultationPrice + $totalMedicines;

            // 5. Generate Invoice
            $invoice = \App\Models\Invoice::create([
                'appointment_id' => $appointment->id,
                'user_id' => $appointment->user_id,
                'total_consultation' => $consultationPrice,
                'total_medicines' => $totalMedicines,
                'grand_total' => $grandTotal,
                'status' => 'unpaid',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Simulasi Berhasil! Dokter selesai memeriksa.',
                'data' => $record->load('prescriptions'), // Balikin data sama obatnya sekalian
                'invoice' => $invoice
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error simulasi: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getPaymentSummary(Request $request, $appointment_id)
    {
        $invoice = \App\Models\Invoice::with([
            'appointment.doctor',
            'appointment.poli',
            'appointment.medical_record.prescriptions'
        ])->where('appointment_id', $appointment_id)
          ->where('user_id', $request->user()->id)
          ->first();

        if (!$invoice) {
            return response()->json([
                'success' => false,
                'message' => 'Invoice belum tersedia untuk appointment ini.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $invoice
        ], 200);
    }

    public function confirmCashierPayment(Request $request, $invoice_id)
    {
        $invoice = \App\Models\Invoice::find($invoice_id);

        if (!$invoice) {
            return response()->json([
                'success' => false,
                'message' => 'Invoice tidak ditemukan.'
            ], 404);
        }

        if ($invoice->status === 'paid') {
            return response()->json([
                'success' => false,
                'message' => 'Invoice ini sudah dibayar.'
            ], 400);
        }

        $invoice->payment_method = 'cashier';
        $invoice->status = 'paid';
        $invoice->save();

        return response()->json([
            'success' => true,
            'message' => 'Pembayaran via kasir berhasil dicatat. Silakan lakukan pembayaran di meja kasir.',
            'data' => $invoice
        ], 200);
    }

    public function selectPaymentMethod(Request $request, $invoiceId)
    {
        $request->validate([
            'payment_method' => 'required|string'
        ]);

        $invoice = \App\Models\Invoice::where('id', $invoiceId)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$invoice) {
            return response()->json([
                'success' => false,
                'message' => 'Invoice not found.'
            ], 404);
        }

        $invoice->payment_method = $request->payment_method;
        $invoice->save();

        return response()->json([
            'success' => true,
            'message' => 'Payment method updated successfully.',
            'data' => $invoice
        ], 200);
    }
}