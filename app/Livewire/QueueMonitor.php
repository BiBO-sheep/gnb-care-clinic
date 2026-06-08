<?php

namespace App\Livewire;

use App\Models\Appointment;
use Carbon\Carbon;
use Livewire\Component;

class QueueMonitor extends Component
{
    public function render()
    {
        $today = Carbon::today()->format('M j, Y');  // Format: "Jun 3, 2026" sesuai format DB
        $todayDate = Carbon::today()->toDateString();  // Format: "2026-06-03" untuk perbandingan >

        // Yang sedang di ruang dokter atau sedang dipanggil (Status: pemeriksaan, check_in)
        $nowServing = Appointment::with(['user', 'poli', 'dokter'])
                        // ->whereRaw("STR_TO_DATE(tanggal, '%b %e, %Y') = ?", [$todayDate]) // DEMO MODE

                        ->whereIn('status', ['pemeriksaan', 'check_in'])
                        ->orderByRaw("CASE WHEN status = 'check_in' THEN 1 ELSE 2 END")
                        ->first();

        // 1. Antrean Hari Ini (Status: scheduled)
        $antreanHariIni = Appointment::with(['user', 'poli'])
                        // ->whereRaw("STR_TO_DATE(tanggal, '%b %e, %Y') = ?", [$todayDate]) // DEMO MODE

                        ->where('status', 'scheduled')
                        ->orderBy('id', 'asc')
                        ->get();

        // 2. Jadwal Mendatang (Status: scheduled)
        $antreanMendatang = Appointment::with(['user', 'poli'])
                        ->whereRaw("STR_TO_DATE(tanggal, '%b %e, %Y') > ?", [$todayDate])
                        ->where('status', 'scheduled')
                        ->orderByRaw("STR_TO_DATE(tanggal, '%b %e, %Y') ASC")
                        ->orderBy('id', 'asc')
                        ->get();

        $totalHariIni = Appointment::count(); // DEMO MODE: menghitung semua

        $selesai = Appointment::where('status', 'selesai')
                    // ->whereRaw("STR_TO_DATE(tanggal, '%b %e, %Y') = ?", [$todayDate]) // DEMO MODE
                    ->count();

        return view('livewire.queue-monitor', compact(
            'nowServing',
            'antreanHariIni',
            'antreanMendatang',
            'totalHariIni',
            'selesai'
        ));
    }
}
