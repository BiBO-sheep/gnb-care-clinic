<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\KlinikController;
use App\Http\Controllers\Api\AppointmentController;
use App\Http\Controllers\Api\MedicalRecordController;
// Rute Publik (Tidak perlu token / Belum Login)
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// Rute Terproteksi (Wajib pakai token dari login)
Route::middleware('auth:sanctum')->group(function () {

    // Auth & Profile
    Route::get('/user', function (Request $request) {
        return response()->json(['status' => 'success', 'data' => $request->user()]);
    });
    Route::get('/profile', [App\Http\Controllers\Api\ProfileController::class, 'show']);
    Route::put('/profile/update', [App\Http\Controllers\Api\ProfileController::class, 'update']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Data Klinik
    Route::get('/poli', [KlinikController::class, 'getPoli']);
    Route::get('/poli/{poli_id}/dokter', [KlinikController::class, 'getDokterByPoli']);
    Route::post('/appointments', [App\Http\Controllers\Api\AppointmentController::class, 'store']);
    Route::get('/queue-status', [App\Http\Controllers\Api\AppointmentController::class, 'getQueueStatus']);
    Route::get('/history', [App\Http\Controllers\Api\AppointmentController::class, 'getHistory']);
    Route::post('/simulate-examination/{id}', [AppointmentController::class, 'simulateExamination']);
    Route::get('/exam-results', [MedicalRecordController::class, 'index']);
    Route::get('/payment-summary/{id}', [AppointmentController::class, 'getPaymentSummary']);
    Route::post('/payment/process', [App\Http\Controllers\Api\PaymentController::class, 'process']);
    Route::post('/payment-method/{invoiceId}', [AppointmentController::class, 'selectPaymentMethod']);
    Route::post('/confirm-cashier-payment/{invoiceId}', [AppointmentController::class, 'confirmCashierPayment']);
});
