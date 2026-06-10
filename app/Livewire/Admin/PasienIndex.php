<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\User;

#[Layout('layouts.admin')]
class PasienIndex extends Component
{
    public $search = '';

    public function render()
    {
        $pasiens = User::where('role', 'pasien')
            ->when($this->search, function($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%')
                      ->orWhere('phone', 'like', '%' . $this->search . '%');
            })
            ->withCount('appointments')
            ->orderBy('name', 'asc')
            ->get();

        return view('livewire.admin.pasien.index', compact('pasiens'));
    }
}
