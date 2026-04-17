<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\KlinikController;

// Rute Publik (Tidak perlu token / Belum Login)
Route::post('/login', [AuthController::class, 'login']);

// Rute Terproteksi (Wajib pakai token dari login)
Route::middleware('auth:sanctum')->group(function () {

    // Auth & Profile
    Route::get('/user', function (Request $request) {
        return response()->json(['status' => 'success', 'data' => $request->user()]);
    });
    Route::post('/logout', [AuthController::class, 'logout']);

    // Data Klinik
    Route::get('/poli', [KlinikController::class, 'getPoli']);
    Route::get('/poli/{poli_id}/dokter', [KlinikController::class, 'getDokterByPoli']);
    Route::post('/appointments', [App\Http\Controllers\Api\AppointmentController::class, 'store']);
    Route::get('/queue-status', [App\Http\Controllers\Api\AppointmentController::class, 'getQueueStatus']);
});
