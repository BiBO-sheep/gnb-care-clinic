<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ReportController;

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
Route::get('/login', function () {
    return redirect('/admin/login');
})->name('login');


// =========================================================
// KEAMANAN TINGKAT 2: BRANKAS KLINIK (WAJIB LOGIN)
// =========================================================
Route::middleware(['auth'])->prefix('klinik')->group(function () {
    
    Route::get('/fix-emails', function() {
        $doctors = \App\Models\User::where('role', 'dokter')->get();
        $updated = [];
        foreach($doctors as $d) {
            $cleanEmail = str_replace(['..', ',', ' '], ['.', '', ''], $d->email);
            $d->email = $cleanEmail;
            $d->save();
            $updated[] = $cleanEmail;
        }
        return "Email Dokter Berhasil Diperbaiki! Berikut daftarnya: <br><br>" . implode("<br>", $updated);
    });

    // =========================================================
    // RUTE UMUM (Dapat Dilihat Admin & Dokter)
    // =========================================================
    Route::middleware([])->group(function () {
        
        // 0. DASHBOARD ANALYTICS (Livewire)
        Route::get('/dashboard', \App\Livewire\Admin\Dashboard::class)->name('dashboard');

        // Laporan & Cetak PDF (Tetap Controller Klasik)
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/export/finance', [ReportController::class, 'exportPdfFinance'])->name('reports.export.finance');
        Route::get('/reports/export/medicine', [ReportController::class, 'exportPdfMedicine'])->name('reports.export.medicine');

        // 1. RUTE MONITOR ANTREAN (Resepsionis)
        Route::get('/queue', function() { return view('admin.queue'); });
        
        // 4. RUTE KASIR (Livewire)
        Route::get('/kasir', \App\Livewire\Admin\KasirIndex::class);

        // 5. RUTE BUKU PASIEN & REKAM MEDIS (Livewire)
        Route::get('/pasien', \App\Livewire\Admin\PasienIndex::class);
        Route::get('/pasien/{id}', \App\Livewire\Admin\PasienShow::class);

        // 6. RUTE MANAJEMEN OBAT (Livewire)
        Route::get('/obat', \App\Livewire\Admin\ObatIndex::class)->name('admin.obat.index');
        Route::get('/obat/create', \App\Livewire\Admin\ObatForm::class)->name('admin.obat.create');
        Route::get('/obat/{id}/edit', \App\Livewire\Admin\ObatForm::class)->name('admin.obat.edit');
    });

    // =========================================================
    // RUTE HALAMAN DOKTER
    // =========================================================
    Route::middleware([])->group(function () {
        
        // 2. RUTE RUANG DOKTER (Ngetik Resep & Periksa) (Livewire)
        Route::get('/doctor', \App\Livewire\Admin\DoctorIndex::class);
        Route::get('/doctor/examine/{id}', \App\Livewire\Admin\DoctorExamine::class);
        
    });

});