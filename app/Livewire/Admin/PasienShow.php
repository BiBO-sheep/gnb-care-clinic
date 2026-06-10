<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\User;

#[Layout('layouts.admin')]
class PasienShow extends Component
{
    public $userId;

    public function mount($id)
    {
        $this->userId = $id;
    }

    public function render()
    {
        $user = User::with([
            'appointments' => function($query) {
                $query->orderBy('created_at', 'desc');
            },
            'appointments.poli',
            'appointments.medical_record.prescriptions',
            'appointments.invoice'
        ])->findOrFail($this->userId);

        return view('livewire.admin.pasien.show', compact('user'));
    }
}
