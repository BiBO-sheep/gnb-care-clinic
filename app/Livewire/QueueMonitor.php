<?php

namespace App\Livewire;

use App\Models\Appointment;
use Carbon\Carbon;
use Livewire\Component;

class QueueMonitor extends Component
{
    public $lastCheckedInIds = [];

    public function cleanupOldData()
    {
        $todayStr = Carbon::today()->toDateString();
        $updated = Appointment::whereRaw("STR_TO_DATE(tanggal, '%b %e, %Y') < ?", [$todayStr])
            ->whereNotIn('status', ['selesai', 'batal', 'kadaluarsa'])
            ->update(['status' => 'kadaluarsa']);

        session()->flash('success', "Berhasil membatalkan {$updated} data testing masa lalu!");
    }

    public function render()
    {
        $today = Carbon::today()->format('M j, Y');  // Format: "Jun 3, 2026" sesuai format DB
        $todayDate = Carbon::today()->toDateString();  // Format: "2026-06-03" untuk perbandingan >

        // Yang sedang di ruang dokter atau sedang dipanggil (Status: pemeriksaan, check_in)
        $nowServing = Appointment::with(['user', 'poli', 'dokter'])
                        // ->whereRaw("STR_TO_DATE(tanggal, '%b %e, %Y') = ?", [$todayDate]) // DEMO MODE

                        ->whereIn('status', ['pemeriksaan', 'check_in'])
                        ->orderByRaw("CASE WHEN status = 'check_in' THEN 1 ELSE 2 END")
                        ->get();

        // ----------------------------------------------------
        // LOGIKA AUDIO NOTIFIKASI
        // ----------------------------------------------------
        $currentCheckedInIds = $nowServing->where('status', 'check_in')->pluck('id')->toArray();
        
        $newCall = null;
        foreach($nowServing as $serving) {
            if ($serving->status == 'check_in' && !in_array($serving->id, $this->lastCheckedInIds)) {
                $newCall = $serving;
                break; // Ambil satu saja untuk di-play agar tidak bentrok
            }
        }
        
        // Cek update_at juga, jika beda, berarti "Panggil Ulang" ditekan
        // Karena waktu terbatas, kita deteksi perubahan ID check_in saja
        
        $this->lastCheckedInIds = $currentCheckedInIds;

        if ($newCall) {
            $this->dispatch('play-audio', [
                'queue_number' => $newCall->queue_number,
                'poli' => $newCall->poli->name ?? 'Poli'
            ]);
        }

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
