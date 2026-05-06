<?php

namespace App\Livewire;

use App\Models\Appointment;
use Carbon\Carbon;
use Livewire\Component;

class QueueMonitor extends Component
{
    public function render()
    {
        $today = Carbon::today()->toDateString();

        // Yang sedang di ruang dokter atau sedang dipanggil (Status: pemeriksaan, check_in)
        $nowServing = Appointment::with(['user', 'poli', 'dokter'])
                        ->whereRaw("STR_TO_DATE(tanggal, '%b %d, %Y') = ?", [$today])
                        ->whereIn('status', ['pemeriksaan', 'check_in'])
                        ->orderByRaw("CASE WHEN status = 'check_in' THEN 1 ELSE 2 END")
                        ->first();

        // 1. Antrean Hari Ini (Status: scheduled)
        $antreanHariIni = Appointment::with(['user', 'poli'])
                        ->whereRaw("STR_TO_DATE(tanggal, '%b %d, %Y') = ?", [$today])
                        ->where('status', 'scheduled')
                        ->orderBy('id', 'asc')
                        ->get();

        // 2. Jadwal Mendatang (Status: scheduled)
        $antreanMendatang = Appointment::with(['user', 'poli'])
                        ->whereRaw("STR_TO_DATE(tanggal, '%b %d, %Y') > ?", [$today])
                        ->where('status', 'scheduled')
                        ->orderByRaw("STR_TO_DATE(tanggal, '%b %d, %Y') ASC")
                        ->orderBy('id', 'asc')
                        ->get();

        $totalHariIni = Appointment::whereRaw("STR_TO_DATE(tanggal, '%b %d, %Y') = ?", [$today])->count();

        $selesai = Appointment::whereRaw("STR_TO_DATE(tanggal, '%b %d, %Y') = ?", [$today])
                    ->where('status', 'selesai')
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
