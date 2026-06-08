<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Appointment;
use Carbon\Carbon;

class CleanupOldAppointments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:cleanup-appointments';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Membatalkan (kadaluarsa) appointment masa lalu yang tidak selesai atau batal.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $todayStr = Carbon::today()->toDateString();
        
        // Update data booking di mana tanggalnya kurang dari hari ini dan statusnya menggantung
        $updated = Appointment::whereRaw("STR_TO_DATE(tanggal, '%b %e, %Y') < ?", [$todayStr])
            ->whereNotIn('status', ['selesai', 'batal', 'kadaluarsa'])
            ->update(['status' => 'kadaluarsa']);

        $this->info("Berhasil mengubah status {$updated} antrean masa lalu menjadi kadaluarsa.");
    }
}
