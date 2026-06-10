<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Obat;

#[Layout('layouts.admin')]
class ObatForm extends Component
{
    public $obatId;
    public $nama_obat;
    public $harga;
    public $stok;
    public $keterangan;

    public $isEdit = false;

    public function mount($id = null)
    {
        if ($id) {
            $this->isEdit = true;
            $this->obatId = $id;
            
            $obat = Obat::findOrFail($id);
            $this->nama_obat = $obat->nama_obat;
            $this->harga = $obat->harga;
            $this->stok = $obat->stok;
            $this->keterangan = $obat->keterangan;
        }
    }

    public function save()
    {
        $this->validate([
            'nama_obat' => 'required|string|max:255',
            'harga' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
            'keterangan' => 'nullable|string',
        ]);

        if ($this->isEdit) {
            $obat = Obat::findOrFail($this->obatId);
            $obat->update([
                'nama_obat' => $this->nama_obat,
                'harga' => $this->harga,
                'stok' => $this->stok,
                'keterangan' => $this->keterangan,
            ]);
            session()->flash('success', 'Data obat berhasil diperbarui.');
        } else {
            Obat::create([
                'nama_obat' => $this->nama_obat,
                'harga' => $this->harga,
                'stok' => $this->stok,
                'keterangan' => $this->keterangan,
            ]);
            session()->flash('success', 'Obat berhasil ditambahkan.');
        }

        return $this->redirect('/klinik/obat', navigate: true);
    }

    public function render()
    {
        return view('livewire.admin.obat.form');
    }
}
