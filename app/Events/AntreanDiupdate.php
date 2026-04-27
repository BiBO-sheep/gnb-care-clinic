<?php

namespace App\Events;

use App\Models\Appointment;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

// Tambahin "implements ShouldBroadcastNow" biar pesannya langsung dikirim detik itu juga
class AntreanDiupdate implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $appointment;

    // Data antrean yang berubah bakal dimasukin ke sini
    public function __construct(Appointment $appointment)
    {
        $this->appointment = $appointment;
    }

    // Nama "Frekuensi Radio" yang bakal kita pakai
    public function broadcastOn(): array
    {
        return [
            new Channel('klinik-channel'),
        ];
    }

    // Nama "Acara Radio"-nya
    public function broadcastAs()
    {
        return 'antrean.berubah';
    }
}