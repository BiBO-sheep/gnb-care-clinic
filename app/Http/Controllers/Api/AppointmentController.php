<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\MedicalRecord;
use App\Models\Prescription;
use Illuminate\Http\Request;
use App\Http\Requests\StoreAppointmentRequest;
use Illuminate\Support\Facades\Auth;

class AppointmentController extends Controller
{
    public function store(StoreAppointmentRequest $request)
    {
        $validated = $request->validated();

        if (!Auth::guard('sanctum')->check()) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $userId = Auth::guard('sanctum')->id();

        $quotaPerSlot = 3;
        $bookedCount = Appointment::where('tanggal', $request->tanggal)
            ->where('poli_id', $request->poli_id)
            ->where('jam', $request->jam)
            ->count();

        if ($bookedCount >= $quotaPerSlot) {
            return response()->json([
                'success' => false,
                'message' => 'Kuota untuk jam ini sudah penuh. Silakan pilih jam lain.'
            ], 400);
        }

        $countSlot = Appointment::where('tanggal', $request->tanggal)
            ->where('poli_id', $request->poli_id)
            ->where('jam', $request->jam)
            ->count();
        
        $jamFormat = str_replace(':', '', $request->jam);
        $queueNumber = 'A-' . $jamFormat . '-' . ($countSlot + 1);

        $appointment = Appointment::create([
            'user_id' => $userId,
            'poli_id' => $request->poli_id,
            'dokter_id' => $request->dokter_id ?? null, // Tambahan jika dokter_id dikirim
            'queue_number' => $queueNumber,
            'tanggal' => $request->tanggal,
            'jam' => $request->jam,
            'status' => 'scheduled'
        ]);

        $appointment->user->notify(new \App\Notifications\AppointmentCreatedNotification());

        return response()->json([
            'success' => true,
            'message' => 'Booking sukses!',
            'data' => $appointment->load('user')
        ], 201);
    }
    
    public function getQueueStatus(Request $request)
    {
        $userId = $request->user()->id;
        $today = date('M d, Y');

        $query = Appointment::where('user_id', $userId)
            ->whereIn('status', ['scheduled', 'check_in', 'pemeriksaan']);

        if (!env('APP_DEMO_MODE', false)) {
            $query->whereRaw("STR_TO_DATE(tanggal, '%b %d, %Y') = STR_TO_DATE(?, '%b %d, %Y')", [$today]);
        }

        $myAppointment = $query->first();

        $nowServing = '-';
        $peopleAhead = 0;

        if ($myAppointment) {
            $nowServingAppt = Appointment::where('tanggal', $myAppointment->tanggal)
                ->where('poli_id', $myAppointment->poli_id)
                ->where('status', 'pemeriksaan')
                ->orderBy('jam', 'asc')
                ->orderBy('id', 'asc')
                ->first();

            $nowServing = $nowServingAppt ? $nowServingAppt->queue_number : 'Persiapan';

            $peopleAhead = Appointment::where('tanggal', $myAppointment->tanggal)
                ->where('poli_id', $myAppointment->poli_id)
                ->whereIn('status', ['scheduled', 'check_in'])
                ->where(function ($query) use ($myAppointment) {
                    $query->where('jam', '<', $myAppointment->jam)
                          ->orWhere(function ($q) use ($myAppointment) {
                              $q->where('jam', $myAppointment->jam)
                                ->where('id', '<', $myAppointment->id);
                          });
                })
                ->count();
        }

        return response()->json([
            'my_queue' => $myAppointment ? $myAppointment->queue_number : '-',
            'now_serving' => $nowServing,
            'people_ahead' => $peopleAhead
        ]);
    }

    public function getAvailableSlots(Request $request)
    {
        $request->validate([
            'tanggal' => 'required',
            'poli_id' => 'required'
        ]);

        $baseSlots = ['09:00', '10:30', '11:15', '14:00', '15:45', '17:45'];
        $quotaPerSlot = 3;

        $appointments = Appointment::where('tanggal', $request->tanggal)
            ->where('poli_id', $request->poli_id)
            ->select('jam', \Illuminate\Support\Facades\DB::raw('count(*) as total'))
            ->groupBy('jam')
            ->get()
            ->keyBy('jam');

        $slots = [];
        foreach ($baseSlots as $jam) {
            $booked = isset($appointments[$jam]) ? $appointments[$jam]->total : 0;
            $slots[] = [
                'jam' => $jam,
                'status' => $booked >= $quotaPerSlot ? 'booked' : 'available',
                'sisa_kuota' => max(0, $quotaPerSlot - $booked)
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $slots
        ]);
    }
    
    public function getHistory(Request $request)
    {
        $userId = $request->user()->id;

        $history = Appointment::with(['poli', 'user', 'dokter'])
            ->where('user_id', $userId)
            ->orderByRaw("STR_TO_DATE(tanggal, '%b %d, %Y') desc")
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
            $consultationPrice = 150000; // Sesuai skenario ISPA

            $prescriptionsData = [
                [
                    'medical_record_id' => $record->id,
                    'medicine_name' => 'Paracetamol 500mg',
                    'dosage' => '10 Tablet',
                    'rules' => '3x1 Sesudah Makan',
                    'price' => 50000,
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

            $appointment->user->notify(new \App\Notifications\ExaminationCompletedNotification());

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

    // Ambil Rincian Tagihan & Resep untuk Flutter
    public function getPaymentSummary($id)
{
    // Kita ambil data appointment + user + rekam medis + obat + invoice
    $appointment = \App\Models\Appointment::with([
        'user', 
        'medical_record.prescriptions', 
        'invoice'
    ])->findOrFail($id);

    return response()->json([
        'status' => 'success',
        'data' => [
            'nama_pasien' => $appointment->user->name, // Ambil nama asli
            'nomor_antrean' => $appointment->queue_number, // Ambil nomor asli
            'status_antrean' => $appointment->status,
            'diagnosis' => $appointment->medical_record->diagnosis ?? '-',
            'medicines' => $appointment->medical_record->prescriptions ?? [],
            'invoice' => $appointment->invoice
        ]
    ]);
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

    public function simulatePaymentSuccess(Request $request, $invoice_id)
    {
        $invoice = \App\Models\Invoice::with('appointment.medical_record.prescriptions')->find($invoice_id);

        if (!$invoice) {
            return response()->json([
                'success' => false,
                'message' => 'Invoice tidak ditemukan.'
            ], 404);
        }

        $invoice->status = 'paid';
        $invoice->payment_method = $request->payment_method ?? $invoice->payment_method;
        $invoice->save();

        // Kurangi stok obat
        $medicalRecord = $invoice->appointment->medical_record ?? null;
        if ($medicalRecord && $medicalRecord->prescriptions) {
            foreach ($medicalRecord->prescriptions as $prescription) {
                if ($prescription->obat_id) {
                    $obat = \App\Models\Obat::find($prescription->obat_id);
                    if ($obat) {
                        $qty = (int) filter_var($prescription->dosage, FILTER_SANITIZE_NUMBER_INT) ?: 1;
                        $obat->stok -= $qty;
                        $obat->save();
                    }
                }
            }
        }

        $invoice->appointment->user->notify(new \App\Notifications\PaymentSuccessNotification());

        return response()->json([
            'success' => true,
            'message' => 'Simulasi pembayaran berhasil, tagihan lunas.',
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

        // Kirim Notifikasi FCM jika status menjadi "pemeriksaan" (Pasien Dipanggil)
        if ($request->status === 'pemeriksaan' && $appointment->user && $appointment->user->fcm_token) {
            try {
                putenv('GOOGLE_APPLICATION_CREDENTIALS=' . storage_path('app/firebase-adminsdk.json'));
                $client = new \Google_Client();
                $client->useApplicationDefaultCredentials();
                $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
                $httpClient = $client->authorize();

                $message = [
                    'message' => [
                        'token' => $appointment->user->fcm_token,
                        'notification' => [
                            'title' => 'Panggilan Antrean!',
                            'body' => 'Giliran Anda telah tiba (Antrean ' . $appointment->queue_number . '). Silakan menuju ruang dokter.'
                        ],
                        'android' => [
                            'priority' => 'high',
                            'notification' => [
                                'channel_id' => 'hospital_call_channel',
                                'sound' => 'tingtung'
                            ]
                        ]
                    ]
                ];

                $projectId = 'gandb-care-clinic';
                $httpClient->post("https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send", [
                    'json' => $message
                ]);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('FCM Send Error: ' . $e->getMessage());
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully.',
            'data' => $appointment
        ], 200);
    }

    public function getActiveQueue(Request $request)
    {
        $today = date('M d, Y'); 

        $query = Appointment::where('user_id', Auth::id())
            ->whereIn('status', ['scheduled', 'check_in', 'pemeriksaan'])
            ->with(['dokter', 'poli'])
            ->orderBy('tanggal', 'asc')
            ->orderBy('jam', 'asc');

        if (!env('APP_DEMO_MODE', false)) {
            $query->whereRaw("STR_TO_DATE(tanggal, '%b %d, %Y') = STR_TO_DATE(?, '%b %d, %Y')", [$today]);
        }

        $appointment = $query->first();

        if (!$appointment) {
            return response()->json([
                'status' => 'success',
                'data' => null
            ]);
        }

        return response()->json([
            'status' => 'success',
            'data' => $appointment
        ]);
    }
}
