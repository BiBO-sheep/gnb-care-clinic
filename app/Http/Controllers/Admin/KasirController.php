<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Prescription;
use App\Models\MedicalRecord;
use Illuminate\Http\Request;

class KasirController extends Controller
{
    public function index()
    {
        $today = \Carbon\Carbon::today()->toDateString();

        // Tagihan menunggu kasir input harga obat
        $pendingKasir = Invoice::with(['user', 'appointment.poli', 'appointment.medical_record.prescriptions'])
                    ->whereHas('appointment', function($q) use ($today) {
                        $q->whereRaw("STR_TO_DATE(tanggal, '%b %d, %Y') = ?", [$today]);
                    })
                    ->where('status', 'pending_kasir')
                    ->orderBy('created_at', 'desc')
                    ->get();

        // Tagihan siap bayar (sudah difinalisasi kasir)
        $invoices = Invoice::with(['user', 'appointment.poli'])
                    ->whereHas('appointment', function($q) use ($today) {
                        $q->whereRaw("STR_TO_DATE(tanggal, '%b %d, %Y') = ?", [$today]);
                    })
                    ->where('status', 'unpaid')
                    ->orderBy('created_at', 'desc')
                    ->get();

        $totalTagihan = $invoices->sum('grand_total');
        $jumlahInvoice = $invoices->count();

        return view('admin.kasir', compact('invoices', 'pendingKasir', 'totalTagihan', 'jumlahInvoice'));
    }

    // Kasir menginput harga obat dan finalisasi tagihan
    public function updateHargaObat(Request $request, $invoiceId)
    {
        $invoice = Invoice::with('appointment.medical_record.prescriptions')->findOrFail($invoiceId);
        $medicalRecord = $invoice->appointment->medical_record;

        if (!$medicalRecord) {
            return back()->with('error', 'Rekam medis tidak ditemukan.');
        }

        $totalMedicines = 0;

        // Update harga masing-masing obat
        if ($request->has('prices') && is_array($request->prices)) {
            foreach ($request->prices as $prescriptionId => $price) {
                $prescription = Prescription::find($prescriptionId);
                if ($prescription) {
                    $prescription->price = (int) $price;
                    $prescription->save();
                    $totalMedicines += (int) $price;
                }
            }
        }

        // Update invoice dengan total obat dan grand total baru
        $invoice->total_medicines = $totalMedicines;
        $invoice->grand_total = $invoice->total_consultation + $totalMedicines;
        $invoice->status = 'unpaid'; // Siap dibayar
        $invoice->save();

        return back()->with('success', 'Harga obat berhasil diperbarui. Tagihan siap dibayar!');
    }

    public function konfirmasiLunas($id)
    {
        $invoice = Invoice::findOrFail($id);
        
        $invoice->update([
            'status' => 'paid',
            'payment_method' => 'cashier'
        ]);

        return back()->with('success', 'Pembayaran atas nama ' . $invoice->user->name . ' Berhasil Dikonfirmasi Lunas!');
    }
}