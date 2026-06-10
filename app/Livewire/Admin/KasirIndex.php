<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Invoice;
use App\Models\Prescription;
use App\Models\MedicalRecord;

#[Layout('layouts.admin')]
class KasirIndex extends Component
{
    public $prices = [];

    public function mount()
    {
        // Tagihan menunggu kasir input harga obat
        $pendingKasir = Invoice::with(['appointment.medical_record.prescriptions'])
                    ->where('status', 'pending_kasir')
                    ->get();
                    
        foreach ($pendingKasir as $inv) {
            $medRec = $inv->appointment->medical_record ?? null;
            if ($medRec && $medRec->prescriptions) {
                foreach ($medRec->prescriptions as $pres) {
                    $this->prices[$pres->id] = $pres->price > 0 ? $pres->price : null;
                }
            }
        }
    }

    public function updateHargaObat($id)
    {
        if (auth()->user()->role === 'dokter') {
            session()->flash('error', 'Akses Ditolak: Dokter tidak boleh mengubah tagihan kasir.');
            return;
        }

        $invoice = Invoice::with('appointment.medical_record.prescriptions')->findOrFail($id);
        $totalMedicines = 0;

        $medRec = $invoice->appointment->medical_record ?? null;
        if ($medRec && $medRec->prescriptions) {
            foreach ($medRec->prescriptions as $pres) {
                $inputPrice = $this->prices[$pres->id] ?? 0;
                $pres->update(['price' => $inputPrice]);
                $totalMedicines += $inputPrice;
            }
        }

        $invoice->update([
            'total_medicines' => $totalMedicines,
            'grand_total'     => $invoice->total_consultation + $totalMedicines,
            'status'          => 'unpaid',
        ]);

        session()->flash('success', 'Harga obat berhasil disimpan! Tagihan siap dibayar.');
    }

    public function konfirmasiLunas($id)
    {
        if (auth()->user()->role === 'dokter') {
            session()->flash('error', 'Akses Ditolak: Dokter tidak boleh memproses pembayaran.');
            return;
        }

        $invoice = Invoice::with('appointment.medical_record.prescriptions')->findOrFail($id);
        
        $invoice->update([
            'status' => 'paid',
            'payment_method' => 'cashier'
        ]);

        $medicalRecord = $invoice->appointment->medical_record ?? null;
        if ($medicalRecord && $medicalRecord->prescriptions) {
            foreach ($medicalRecord->prescriptions as $prescription) {
                if ($prescription->obat_id) {
                    $obat = \App\Models\Obat::find($prescription->obat_id);
                    if ($obat) {
                        $qty = (int) filter_var($prescription->dosage, FILTER_SANITIZE_NUMBER_INT) ?: 1;
                        $obat->stok -= $qty;
                        $obat->save();
                    }
                }
            }
        }

        if ($invoice->appointment->user) {
            $invoice->appointment->user->notify(new \App\Notifications\PaymentSuccessNotification());
        }

        session()->flash('success', 'Pembayaran lunas! Stok obat otomatis dikurangi dan notifikasi dikirim.');
    }

    public function render()
    {
        $pendingKasir = Invoice::with(['user', 'appointment.poli', 'appointment.medical_record.prescriptions'])
                    ->where('status', 'pending_kasir')
                    ->orderBy('created_at', 'desc')
                    ->get();

        $invoices = Invoice::with(['user', 'appointment.poli'])
                    ->where('status', 'unpaid')
                    ->orderBy('created_at', 'desc')
                    ->get();

        $totalTagihan = $invoices->sum('grand_total');
        $jumlahInvoice = $invoices->count();

        return view('livewire.admin.kasir-index', compact('invoices', 'pendingKasir', 'totalTagihan', 'jumlahInvoice'));
    }
}
