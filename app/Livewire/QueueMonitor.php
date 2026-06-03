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
                        ->where('tanggal', $today)
                        ->whereIn('status', ['pemeriksaan', 'check_in'])
                        ->orderByRaw("CASE WHEN status = 'check_in' THEN 1 ELSE 2 END")
                        ->first();

        // 1. Antrean Hari Ini (Status: scheduled)
        $antreanHariIni = Appointment::with(['user', 'poli'])
                        ->where('tanggal', $today)
                        ->where('status', 'scheduled')
                        ->orderBy('id', 'asc')
                        ->get();

        // 2. Jadwal Mendatang (Status: scheduled)
        $antreanMendatang = Appointment::with(['user', 'poli'])
                        ->where('tanggal', '>', $today)
                        ->where('status', 'scheduled')
                        ->orderBy('tanggal', 'asc')
                        ->orderBy('id', 'asc')
                        ->get();

        $totalHariIni = Appointment::where('tanggal', $today)->count();

        $selesai = Appointment::where('tanggal', $today)
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
