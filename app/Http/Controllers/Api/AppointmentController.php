<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\MedicalRecord;
use App\Models\Prescription;
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

        if (!Auth::guard('sanctum')->check()) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $userId = Auth::guard('sanctum')->id();

        $countToday = Appointment::where('tanggal', $request->tanggal)->count();
        $queueNumber = 'A-' . ($countToday + 1);

        $appointment = Appointment::create([
            'user_id' => $userId,
            'poli_id' => $request->poli_id,
            'dokter_id' => $request->dokter_id ?? null, // Tambahan jika dokter_id dikirim
            'queue_number' => $queueNumber,
            'tanggal' => $request->tanggal,
            'jam' => $request->jam,
            'status' => 'scheduled'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Booking sukses!',
            'data' => $appointment->load('user')
        ], 201);
    }
    
    public function getQueueStatus(Request $request)
    {
        $userId = $request->user()->id;
        $today = date('M d, 2026');

        $myAppointment = Appointment::where('user_id', $userId)
            ->where('tanggal', $today)
            ->whereIn('status', ['scheduled', 'check_in', 'pemeriksaan'])
            ->first();

        $nowServing = Appointment::where('tanggal', $today)
            ->where('status', 'pemeriksaan')
            ->min('queue_number');

        return response()->json([
            'my_queue' => $myAppointment ? $myAppointment->queue_number : 'A-0',
            'now_serving' => $nowServing ?? 'A-1',
        ]);
    }
    
    public function getHistory(Request $request)
    {
        $userId = $request->user()->id;

        $history = Appointment::with(['poli', 'user', 'dokter'])
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
            $appointment = Appointment::findOrFail($id);

            if ($appointment->status === 'selesai') {
                return response()->json(['success' => false, 'message' => 'Pasien ini sudah diperiksa.'], 400);
            }

            $appointment->status = 'selesai';
            $appointment->save();

            $doctorId = $appointment->dokter_id ?? 1;
            
            $record = MedicalRecord::create([
                'user_id' => $appointment->user_id,
                'appointment_id' => $appointment->id,
                'doctor_id' => $doctorId, 
                'diagnosis' => 'Simulasi: Infeksi Saluran Pernapasan Akut (ISPA)',
                'doctor_notes' => 'Tenggorokan pasien terlihat merah. Disarankan banyak minum air putih.',
                'treatment_plan' => 'Istirahat total 2 hari, hindari minuman dingin dan berminyak.',
            ]);

            $doctor = \App\Models\User::where('role', 'dokter')->find($doctorId) 
                      ?? \App\Models\User::where('role', 'dokter')->first();
            $consultationPrice = $doctor ? $doctor->price : 100000;

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
            \App\Models\Prescription::insert($prescriptionsData);

            $totalMedicines = collect($prescriptionsData)->sum('price');
            $grandTotal = $consultationPrice + $totalMedicines;

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
                'data' => $record->load('prescriptions'),
                'invoice' => $invoice
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error simulasi: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getPaymentSummary(Request $request, $id)
    {
        try {
            $invoice = \App\Models\Invoice::with([
                'appointment.poli',
                'appointment.medical_record.doctor',
                'appointment.medical_record.prescriptions'
            ])->where('appointment_id', $id)
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
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
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

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string'
        ]);

        $appointment = Appointment::findOrFail($id);
        $appointment->status = $request->status;
        $appointment->save();

        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully.',
            'data' => $appointment
        ], 200);
    }
}