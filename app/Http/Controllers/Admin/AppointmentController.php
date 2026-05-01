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
use App\Events\AntreanDiupdate;

class AppointmentController extends Controller
{
    public function queue()
    {
        $today = \Carbon\Carbon::today()->toDateString();

        // Yang lagi di dalem ruang dokter (Status: pemeriksaan, hanya untuk hari ini)
        $nowServing = Appointment::with(['user', 'poli', 'dokter'])
                        ->whereRaw("STR_TO_DATE(tanggal, '%b %d, %Y') = ?", [$today])
                        ->where('status', 'pemeriksaan')
                        ->first();

        // 1. Antrean Hari Ini (Status: scheduled)
        $antreanHariIni = Appointment::with(['user', 'poli'])
                        ->whereRaw("STR_TO_DATE(tanggal, '%b %d, %Y') = ?", [$today])
                        ->where('status', 'scheduled')
                        ->orderBy('id', 'asc')
                        ->get();

        // 2. Jadwal Mendatang (Status: scheduled)
        $antreanMendatang = Appointment::with(['user', 'poli'])
                        ->whereRaw("STR_TO_DATE(tanggal, '%b %d, %Y') > ?", [$today])
                        ->where('status', 'scheduled')
                        ->orderByRaw("STR_TO_DATE(tanggal, '%b %d, %Y') ASC")
                        ->orderBy('id', 'asc')
                        ->get();

        $totalHariIni = Appointment::whereRaw("STR_TO_DATE(tanggal, '%b %d, %Y') = ?", [$today])->count();
        $selesai = Appointment::whereRaw("STR_TO_DATE(tanggal, '%b %d, %Y') = ?", [$today])
                    ->where('status', 'selesai')
                    ->count();

        return view('admin.queue', compact('nowServing', 'antreanHariIni', 'antreanMendatang', 'totalHariIni', 'selesai'));
    }

    // 1. Fungsi Panggil Pasien (Ubah status jadi check_in)
    public function callPasien($id)
    {
        $appointment = Appointment::findOrFail($id);
        $appointment->update(['status' => 'check_in']);

        // 👇 TOA DIBUNYIKAN: Pasien Dipanggil! 👇
        broadcast(new AntreanDiupdate($appointment));

        return back()->with('success', 'Pasien nomor ' . $appointment->queue_number . ' dipanggil!');
    }

    // 2. Fungsi Pasien Masuk Ruangan (Ubah status jadi pemeriksaan)
    public function masukDokter($id)
    {
        $appointment = Appointment::findOrFail($id);
        $appointment->update(['status' => 'pemeriksaan']);

        // 👇 TOA DIBUNYIKAN: Pasien Masuk Ruangan! 👇
        broadcast(new AntreanDiupdate($appointment));

        return redirect('/klinik/doctor')->with('success', 'Pasien sudah berada di ruang dokter.');
    }

    // 3. Fungsi Simpan Resep & Tagihan (Mirip banget sama API Simulate lu)
    public function simpanResep(Request $request, $id)
    {
        $appointment = Appointment::findOrFail($id);

        DB::beginTransaction();
        try {
            // A. Tentukan Harga Dokter
            $dokterId = $appointment->dokter_id ?? 1;
            $doctor = User::where('role', 'dokter')->find($dokterId);
            $consultationPrice = $doctor ? $doctor->price : 100000;

            // B. Buat Rekam Medis (Medical Record)
            $record = MedicalRecord::create([
                'user_id' => $appointment->user_id,
                'doctor_id' => $dokterId,
                'appointment_id' => $appointment->id,
                'diagnosis' => $request->diagnosis,
                'doctor_notes' => 'Tindakan selesai dilakukan di ruang dokter.',
                'treatment_plan' => 'Silakan tebus resep obat dan istirahat.',
            ]);

            $totalMedicines = 0;

            // C. Masukkan Daftar Obat ke tabel Prescriptions
            if ($request->has('medicines') && is_array($request->medicines)) {
                foreach ($request->medicines as $med) {
                    if (!empty($med['name'])) {
                        $hargaObat = 25000; // Harga default obat (bisa disesuaikan nanti)
                        
                        Prescription::create([
                            'medical_record_id' => $record->id,
                            'medicine_name' => $med['name'],
                            'dosage' => $med['qty'] . ' Pcs',
                            'rules' => $med['rules'],
                            'price' => $hargaObat,
                        ]);
                        $totalMedicines += $hargaObat;
                    }
                }
            }

            // D. Buat Tagihan Kasir (Invoice)
            // Bagian simpanResep di Admin/AppointmentController.php
Invoice::create([
    'appointment_id' => $appointment->id,
    'user_id' => $appointment->user_id,
    'total_consultation' => $consultationPrice, // <-- Pastikan ini HANYA harga dokter
    'total_medicines' => $totalMedicines,       // <-- Pastikan ini HANYA harga obat
    'grand_total' => $consultationPrice + $totalMedicines, // <-- Ini baru penjumlahannya
    'status' => 'unpaid',
]);

            // E. Ubah Status Antrean Selesai
            $appointment->update(['status' => 'selesai']);

            DB::commit(); // Save dulu datanya dengan aman ke Database

            // 👇 TOA DIBUNYIKAN: Pemeriksaan Selesai, tagihan meluncur! 👇
            broadcast(new AntreanDiupdate($appointment));

            return redirect('/klinik/queue')->with('success', 'Pemeriksaan selesai! Resep & Tagihan berhasil dikirim ke HP Pasien.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }
}