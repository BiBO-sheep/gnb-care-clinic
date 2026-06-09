<?php

use Illuminate\Support\Facades\Route;
use App\Models\Appointment;
use App\Http\Controllers\Admin\AppointmentController;
use App\Http\Controllers\Admin\KasirController;
use App\Http\Controllers\Admin\DoctorController;
use App\Http\Controllers\Admin\PasienController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ObatController;

Route::get('/', function () {
    return view('welcome'); 
});

// Fallback rute untuk foto profil jika symlink gagal di shared hosting
Route::get('/storage/avatars/{filename}', function ($filename) {
    $path = storage_path('app/public/avatars/' . $filename);
    if (!\Illuminate\Support\Facades\File::exists($path)) {
        abort(404);
    }
    return response()->file($path);
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
    
    // 0. DASHBOARD ANALYTICS
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/dashboard/cleanup', [DashboardController::class, 'cleanupBugData'])->name('dashboard.cleanup');

    // Laporan & Cetak PDF
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export/finance', [ReportController::class, 'exportPdfFinance'])->name('reports.export.finance');
    Route::get('/reports/export/medicine', [ReportController::class, 'exportPdfMedicine'])->name('reports.export.medicine');

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
    Route::post('/kasir/{id}/harga-obat', [KasirController::class, 'updateHargaObat']);

    // 5. RUTE BUKU PASIEN & REKAM MEDIS
    Route::get('/pasien', [PasienController::class, 'index']);
    Route::get('/pasien/{id}', [PasienController::class, 'show']);

    // 6. RUTE MANAJEMEN OBAT
    Route::resource('obat', ObatController::class, ['as' => 'admin']);
});