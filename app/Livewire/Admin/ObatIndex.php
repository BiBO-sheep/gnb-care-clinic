<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use App\Models\Obat;

#[Layout('layouts.admin')]
class ObatIndex extends Component
{
    use WithPagination;

    public $search = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function deleteObat($id)
    {
        $obat = Obat::findOrFail($id);
        $obat->delete();
        session()->flash('success', 'Obat berhasil dihapus.');
    }

    public function render()
    {
        $query = Obat::query();

        if ($this->search) {
            $query->where('nama_obat', 'like', "%{$this->search}%");
        }

        $obats = $query->orderBy('nama_obat')->paginate(10);

        return view('livewire.admin.obat.index', compact('obats'));
    }
}
