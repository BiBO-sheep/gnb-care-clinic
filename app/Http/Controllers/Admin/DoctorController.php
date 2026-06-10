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

        $user = auth()->user();

        // Tampilkan pasien yang sudah check-in (siap diperiksa, hanya hari ini)
        $waitingQuery = Appointment::with(['user', 'poli'])
                            ->whereRaw("STR_TO_DATE(tanggal, '%b %e, %Y') = ?", [$today])
                            ->where('status', 'check_in')
                            ->orderBy('id', 'asc');
                            
        if ($user && $user->role === 'dokter' && $user->poli_id) {
            $waitingQuery->where('poli_id', $user->poli_id);
        }
        $waitingPatients = $waitingQuery->get();

        // Cek apakah ada pasien yang sedang "nyangkut" di status pemeriksaan (hanya hari ini)
        $activeQuery = Appointment::with(['user', 'poli'])
                            ->whereRaw("STR_TO_DATE(tanggal, '%b %e, %Y') = ?", [$today])
                            ->where('status', 'pemeriksaan');
                            
        if ($user && $user->role === 'dokter' && $user->poli_id) {
            $activeQuery->where('poli_id', $user->poli_id);
        }
        $activePatient = $activeQuery->first();

        return view('admin.doctor.index', compact('waitingPatients', 'activePatient'));
    }

    public function periksa($id)
    {
        $appointment = Appointment::with(['user', 'poli', 'dokter'])->findOrFail($id);
        $user = auth()->user();

        // VALIDASI: Dokter dilarang periksa pasien poli lain!
        if ($user->role === 'dokter' && $user->poli_id !== $appointment->poli_id) {
            abort(403, 'Akses Ditolak: Pasien ini terdaftar di poli lain.');
        }
        
        // Update status ke 'pemeriksaan' HANYA jika yang klik adalah dokter
        if ($user->role !== 'admin' && $appointment->status !== 'pemeriksaan') {
            $appointment->update(['status' => 'pemeriksaan']);
        }
        
        // Ambil data obat yang stoknya masih ada
        $obats = \App\Models\Obat::where('stok', '>', 0)->get();

        return view('admin.doctor.examine', compact('appointment', 'obats'));
    }
}