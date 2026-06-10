<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\MedicalRecord;
use App\Models\Prescription;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AppointmentController extends Controller
{
    public function queue()
    {
        // Query dipindahkan ke komponen Livewire QueueMonitor (wire:poll.3s)
        return view('admin.queue');
    }

    public function callPasien($id)
    {
        abort_if(auth()->user()->role === 'dokter', 403, 'Akses Ditolak: Dokter tidak boleh memanggil pasien.');
        $appointment = Appointment::findOrFail($id);
        $appointment->status = 'check_in';
        $appointment->touch(); // Paksa update timestamp updated_at biar Flutter deteksi 'Panggil Ulang'
        $appointment->save();

        // Kirim Notifikasi FCM untuk panggil pasien (Audio & Notif Flutter)
        if ($appointment->user && $appointment->user->fcm_token) {
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
                            'body' => 'Giliran Anda telah tiba (Antrean ' . $appointment->queue_number . '). Silakan menuju ' . ($appointment->poli->name ?? 'ruangan') . '.'
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

        return back()->with('success', 'Pasien nomor ' . $appointment->queue_number . ' dipanggil!');
    }

    // 2. Fungsi Pasien Masuk Ruangan (Ubah status jadi pemeriksaan)
    public function masukDokter($id)
    {
        $appointment = Appointment::findOrFail($id);
        $appointment->update(['status' => 'pemeriksaan']);

        return redirect('/klinik/doctor')->with('success', 'Pasien sudah berada di ruang dokter.');
    }

    // 3. Fungsi Simpan Resep & Tagihan
    public function simpanResep(Request $request, $id)
    {
        $appointment = Appointment::findOrFail($id);
        $user = auth()->user();

        // VALIDASI: Admin tidak boleh simpan resep
        if ($user->role === 'admin') {
            abort(403, 'Akses Ditolak: Admin tidak diizinkan mengisi rekam medis.');
        }

        // VALIDASI: Dokter hanya boleh simpan resep untuk pasien di polinya sendiri
        if ($user->role === 'dokter' && $user->poli_id !== $appointment->poli_id) {
            abort(403, 'Akses Ditolak: Anda tidak dapat memeriksa pasien dari poli lain.');
        }

        DB::beginTransaction();
        try {
            // A. Harga Jasa Dokter (Otomatis dari DB)
            $consultationPrice = $appointment->dokter->price ?? 150000;

            // B. Buat Rekam Medis (Medical Record)
            $record = MedicalRecord::create([
                'user_id'        => $appointment->user_id,
                'doctor_id'      => $appointment->dokter_id ?? 1,
                'appointment_id' => $appointment->id,
                'keluhan'        => $request->keluhan,
                'diagnosis'      => $request->diagnosis,
                'tindakan'       => $request->tindakan,
                'doctor_notes'   => 'Selesai diperiksa dokter.',
                'treatment_plan' => $request->tindakan,
            ]);

            // C. Masukkan Daftar Obat ke tabel Prescriptions (Hitung harga otomatis)
            $totalMedicines = 0;
            if ($request->has('medicines') && is_array($request->medicines)) {
                foreach ($request->medicines as $med) {
                    if (!empty($med['obat_id'])) {
                        $obat = \App\Models\Obat::find($med['obat_id']);
                        if ($obat) {
                            $qty = $med['qty'] ?? 1;
                            $price = $obat->harga * $qty;
                            $totalMedicines += $price;

                            Prescription::create([
                                'medical_record_id' => $record->id,
                                'obat_id'           => $obat->id,
                                'medicine_name'     => $obat->nama_obat,
                                'dosage'            => $qty . ' Pcs',
                                'rules'             => $med['rules'] ?? '-',
                                'price'             => $price,
                            ]);
                        }
                    }
                }
            }

            // D. Buat Invoice — total_medicines diisi langsung dari perhitungan, status = unpaid
            Invoice::create([
                'appointment_id'    => $appointment->id,
                'user_id'           => $appointment->user_id,
                'total_consultation'=> $consultationPrice,
                'total_medicines'   => $totalMedicines,
                'grand_total'       => $consultationPrice + $totalMedicines,
                'status'            => 'unpaid',    // Siap dibayar, kasir tidak perlu input
            ]);

            // E. Ubah Status Antrean Selesai
            $appointment->update(['status' => 'selesai']);

            // Kirim Notifikasi Database ke Aplikasi Flutter
            $appointment->user->notify(new \App\Notifications\ExaminationCompletedNotification());

            // Kirim Notifikasi FCM agar Flutter tahu status berubah dan bisa auto-refresh history
            if ($appointment->user && $appointment->user->fcm_token) {
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
                                'title' => 'Pemeriksaan Selesai',
                                'body' => 'Pemeriksaan selesai. Resep dan tagihan sudah terbit.'
                            ],
                            'android' => [
                                'priority' => 'high'
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

            DB::commit();

            return redirect('/klinik/queue')->with('success', 'Pemeriksaan selesai! Tagihan otomatis dihitung dan siap dibayar oleh Kasir.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }
}