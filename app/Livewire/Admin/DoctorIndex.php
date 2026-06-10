<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Appointment;

#[Layout('layouts.admin')]
class DoctorIndex extends Component
{
    // Auto-refresh using wire:poll logic in view
    
    public function render()
    {
        $today = \Carbon\Carbon::today()->toDateString();
        $user = auth()->user();

        $waitingQuery = Appointment::with(['user', 'poli'])
                            // ->whereRaw("STR_TO_DATE(tanggal, '%b %e, %Y') = ?", [$today]) // DEMO MODE
                            ->where('status', 'check_in')
                            ->orderBy('id', 'asc');
                            
        $activeQuery = Appointment::with(['user', 'poli'])
                            // ->whereRaw("STR_TO_DATE(tanggal, '%b %e, %Y') = ?", [$today]) // DEMO MODE
                            ->where('status', 'pemeriksaan');
                            
        if ($user && $user->role === 'dokter') {
            // DEMO MODE: Biarkan dokter melihat semua antrean (tidak di-filter by poli) agar presentasi lancar
            // Jika mau strict: tambahkan $query->where('poli_id', $user->poli_id)
            $waitingPatients = $waitingQuery->get();
            $activePatient = $activeQuery->first();
            $activePatients = null; 
            $isAdmin = false;
        } else {
            // Admin Mode: See all, group by poli
            $waitingPatients = $waitingQuery->get()->groupBy('poli.name');
            $activePatients = $activeQuery->get()->groupBy('poli.name');
            $activePatient = null;
            $isAdmin = true;
        }

        return view('livewire.admin.doctor.index', compact('waitingPatients', 'activePatient', 'activePatients', 'isAdmin'));
    }
}
