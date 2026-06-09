<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Http\Request;

class DoctorController extends Controller
{
    public function index()
    {
        $today = \Carbon\Carbon::today()->toDateString();

        // Tampilkan pasien yang sudah check-in (siap diperiksa, hanya hari ini)
        $waitingPatients = Appointment::with(['user', 'poli'])
                            // ->whereRaw("STR_TO_DATE(tanggal, '%b %e, %Y') = ?", [$today]) // DEMO MODE

                            ->where('status', 'check_in')
                            ->orderBy('id', 'asc')
                            ->get();

        // Cek apakah ada pasien yang sedang "nyangkut" di status pemeriksaan (hanya hari ini)
        $activePatient = Appointment::with(['user', 'poli'])
                            // ->whereRaw("STR_TO_DATE(tanggal, '%b %e, %Y') = ?", [$today]) // DEMO MODE

                            ->where('status', 'pemeriksaan')
                            ->first();

        return view('admin.doctor.index', compact('waitingPatients', 'activePatient'));
    }

    public function periksa($id)
    {
        $appointment = Appointment::with(['user', 'poli', 'dokter'])->findOrFail($id);
        
        // Update status ke 'pemeriksaan' agar sinkron ke Flutter & Antrean Depan
        $appointment->update(['status' => 'pemeriksaan']);
        
        // Ambil data obat yang stoknya masih ada
        $obats = \App\Models\Obat::where('stok', '>', 0)->get();

        return view('admin.doctor.examine', compact('appointment', 'obats'));
    }
}