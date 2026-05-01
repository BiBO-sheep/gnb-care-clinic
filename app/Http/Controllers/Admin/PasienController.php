<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class PasienController extends Controller
{
    public function index()
    {
        // Ambil data user yang memiliki role 'pasien'
        $pasiens = User::where('role', 'pasien')
            ->withCount('appointments')
            ->orderBy('name', 'asc')
            ->get();

        return view('admin.pasien.index', compact('pasiens'));
    }

    public function show($id)
    {
        // Ambil detail pasien dengan semua riwayat medisnya
        $user = User::with([
            'appointments' => function($query) {
                $query->orderBy('created_at', 'desc');
            },
            'appointments.poli',
            'appointments.medical_record.prescriptions',
            'appointments.invoice'
        ])->findOrFail($id);

        return view('admin.pasien.show', compact('user'));
    }
}
