<?php

use Illuminate\Support\Facades\Route;

// Rute untuk Landing Page (Tanpa perlu login)
Route::get('/', function () {
    return view('welcome'); // Kita timpa file welcome.blade.php bawaan Laravel
});

// Rute-rute lain punya lu di bawah sini...