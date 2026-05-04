<?php

use Illuminate\Support\Facades\Route;
use App\Models\Appointment;
use App\Http\Controllers\Admin\AppointmentController;
use App\Http\Controllers\Admin\KasirController;
use App\Http\Controllers\Admin\DoctorController;
use App\Http\Controllers\Admin\PasienController;

Route::get('/', function () {
    return view('welcome'); 
});

// =========================================================
// KEAMANAN TINGKAT 1: POS SATPAM (LOGIN REDIRECT)
// =========================================================
// Wajib ada! Biar kalau ada hacker/user iseng yang belum login 
// mau nyoba nembus URL /klinik, dia otomatis ditendang ke Filament.
Route::get('/login', function () {
    return redirect('/admin/login');
})->name('login');


// =========================================================
// KEAMANAN TINGKAT 2: BRANKAS KLINIK (WAJIB LOGIN)
// =========================================================
// Semua rute di dalam grup ini sudah dilindungi middleware 'auth'.
// Tidak ada yang bisa masuk tanpa akun Admin/Dokter/Resepsionis.
Route::middleware(['auth'])->prefix('klinik')->group(function () {
    
    // 1. RUTE MONITOR ANTREAN (Resepsionis)
    Route::get('/queue', [AppointmentController::class, 'queue']);

    // 2. RUTE RUANG DOKTER (Ngetik Resep)
    Route::get('/doctor', [DoctorController::class, 'index']);
    Route::get('/doctor/examine/{id}', [DoctorController::class, 'periksa']);

    // 3. RUTE ACTION ADMIN (Tombol Panggil, Masuk, Selesai)
    Route::post('/appointment/{id}/call', [AppointmentController::class, 'callPasien']);
    Route::post('/appointment/{id}/progress', [AppointmentController::class, 'masukDokter']);
    Route::post('/appointment/{id}/prescribe', [AppointmentController::class, 'simpanResep']);
    
    // 4. RUTE KASIR
    Route::get('/kasir', [KasirController::class, 'index']);
    Route::post('/kasir/{id}/lunas', [KasirController::class, 'konfirmasiLunas']);

    // 5. RUTE BUKU PASIEN & REKAM MEDIS
    Route::get('/pasien', [PasienController::class, 'index']);
    Route::get('/pasien/{id}', [PasienController::class, 'show']);
});