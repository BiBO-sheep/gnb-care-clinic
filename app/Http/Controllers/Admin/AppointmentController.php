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

    // 1. Fungsi Panggil Pasien (Ubah status jadi check_in)
    public function callPasien($id)
    {
        $appointment = Appointment::findOrFail($id);
        $appointment->status = 'check_in';
        $appointment->touch(); // Paksa update timestamp updated_at biar Flutter deteksi 'Panggil Ulang'
        $appointment->save();

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

        DB::beginTransaction();
        try {
            // A. Harga Jasa Dokter dari INPUT FORM (bukan auto dari DB)
            $consultationPrice = (int) $request->input('consultation_fee', 0);

            // B. Buat Rekam Medis (Medical Record)
            $record = MedicalRecord::create([
                'user_id'        => $appointment->user_id,
                'doctor_id'      => $appointment->dokter_id ?? 1,
                'appointment_id' => $appointment->id,
                'diagnosis'      => $request->diagnosis,
                'doctor_notes'   => 'Tindakan selesai dilakukan di ruang dokter.',
                'treatment_plan' => 'Silakan tebus resep obat dan istirahat.',
            ]);

            // C. Masukkan Daftar Obat ke tabel Prescriptions (harga = 0, diisi kasir nanti)
            if ($request->has('medicines') && is_array($request->medicines)) {
                foreach ($request->medicines as $med) {
                    if (!empty($med['name'])) {
                        Prescription::create([
                            'medical_record_id' => $record->id,
                            'medicine_name'     => $med['name'],
                            'dosage'            => ($med['qty'] ?? 1) . ' Pcs',
                            'rules'             => $med['rules'] ?? '-',
                            'price'             => 0, // Harga diisi oleh Kasir
                        ]);
                    }
                }
            }

            // D. Buat Invoice awal — total_medicines = 0 dulu, Kasir yang finalisasi
            Invoice::create([
                'appointment_id'    => $appointment->id,
                'user_id'           => $appointment->user_id,
                'total_consultation'=> $consultationPrice,
                'total_medicines'   => 0,
                'grand_total'       => $consultationPrice, // Sementara hanya jasa dokter
                'status'            => 'pending_kasir',    // Status baru: menunggu kasir input harga obat
            ]);

            // E. Ubah Status Antrean Selesai
            $appointment->update(['status' => 'selesai']);

            DB::commit();

            return redirect('/klinik/queue')->with('success', 'Pemeriksaan selesai! Resep dikirim ke Kasir untuk penghitungan harga obat.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }
}