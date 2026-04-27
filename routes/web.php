<?php

use Illuminate\Support\Facades\Route;
use App\Models\Appointment;
use App\Http\Controllers\Admin\AppointmentController;

Route::get('/', function () {
    return view('welcome'); 
});

// 1. RUTE MONITOR ANTREAN (Resepsionis)
Route::get('/admin/queue', function () { 
    // Yang lagi di dalem ruang dokter (Status: pemeriksaan)
    $nowServing = Appointment::with(['user', 'poli', 'dokter'])
                    ->where('status', 'pemeriksaan')
                    ->first();

    // Daftar tunggu hari ini (Status: scheduled / check_in)
    // Catatan: Karena di API lu tanggalnya pake format text, kita pastiin sort by ID aja biar gampang
    $waitingList = Appointment::with(['user', 'poli'])
                    ->whereIn('status', ['scheduled', 'check_in'])
                    ->orderBy('id', 'asc')
                    ->get();

    $totalHariIni = Appointment::count();
    $selesai = Appointment::where('status', 'selesai')->count();

    return view('admin.queue', compact('nowServing', 'waitingList', 'totalHariIni', 'selesai')); 
});

// 2. RUTE RUANG DOKTER (Ngetik Resep)
Route::get('/admin/doctor', function () {
    // Cari pasien yang statusnya 'pemeriksaan'
    $activePatient = Appointment::with(['user', 'poli'])
                    ->where('status', 'pemeriksaan')
                    ->first();

    return view('admin.doctor', compact('activePatient'));
});

// 3. RUTE ACTION ADMIN (Tombol Panggil, Masuk, Selesai)
Route::post('/admin/appointment/{id}/call', [AppointmentController::class, 'callPasien']);
Route::post('/admin/appointment/{id}/progress', [AppointmentController::class, 'masukDokter']);
Route::post('/admin/appointment/{id}/prescribe', [AppointmentController::class, 'simpanResep']);