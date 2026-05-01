<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Http\Request;
use App\Events\AntreanDiupdate;

class DoctorController extends Controller
{
    public function index()
    {
        $today = \Carbon\Carbon::today()->toDateString();

        // Tampilkan pasien yang sudah check-in (siap diperiksa, hanya hari ini)
        $waitingPatients = Appointment::with(['user', 'poli'])
                            ->whereRaw("STR_TO_DATE(tanggal, '%b %d, %Y') = ?", [$today])
                            ->where('status', 'check_in')
                            ->orderBy('id', 'asc')
                            ->get();

        // Cek apakah ada pasien yang sedang "nyangkut" di status pemeriksaan (hanya hari ini)
        $activePatient = Appointment::with(['user', 'poli'])
                            ->whereRaw("STR_TO_DATE(tanggal, '%b %d, %Y') = ?", [$today])
                            ->where('status', 'pemeriksaan')
                            ->first();

        return view('admin.doctor.index', compact('waitingPatients', 'activePatient'));
    }

    public function periksa($id)
    {
        $appointment = Appointment::with(['user', 'poli'])->findOrFail($id);
        
        // Update status ke 'pemeriksaan' agar sinkron ke Flutter & Antrean Depan
        $appointment->update(['status' => 'pemeriksaan']);
        
        // Beritahu sistem (WebSocket) ada update status
        broadcast(new AntreanDiupdate($appointment));

        return view('admin.doctor.examine', compact('appointment'));
    }
}