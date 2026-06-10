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
                    // DEMO MODE ->
                    /* ->whereHas('appointment', function($q) use ($today) {
                        $q->whereRaw("STR_TO_DATE(tanggal, '%b %e, %Y') = ?", [$today]);
                    }) */
                    ->where('status', 'pending_kasir')
                    ->orderBy('created_at', 'desc')
                    ->get();

        // Tagihan siap bayar (sudah difinalisasi kasir)
        $invoices = Invoice::with(['user', 'appointment.poli'])
                    // DEMO MODE ->
                    /* ->whereHas('appointment', function($q) use ($today) {
                        $q->whereRaw("STR_TO_DATE(tanggal, '%b %e, %Y') = ?", [$today]);
                    }) */
                    ->where('status', 'unpaid')
                    ->orderBy('created_at', 'desc')
                    ->get();

        $totalTagihan = $invoices->sum('grand_total');
        $jumlahInvoice = $invoices->count();

        return view('admin.kasir', compact('invoices', 'pendingKasir', 'totalTagihan', 'jumlahInvoice'));
    }

    // Kasir tidak lagi menginput harga karena sudah otomatis dari Master Obat


    public function pay(Request $request, $id)
    {
        abort_if(auth()->user()->role === 'dokter', 403, 'Akses Ditolak: Dokter tidak boleh memproses pembayaran.');
        $invoice = Invoice::with('appointment.medical_record.prescriptions')->findOrFail($id);
        
        // Update status tagihan jadi lunas
        $invoice->update([
            'status' => 'paid',
            'payment_method' => 'cashier'
        ]);

        // Kurangi stok obat
        $medicalRecord = $invoice->appointment->medical_record ?? null;
        if ($medicalRecord && $medicalRecord->prescriptions) {
            foreach ($medicalRecord->prescriptions as $prescription) {
                if ($prescription->obat_id) {
                    $obat = \App\Models\Obat::find($prescription->obat_id);
                    if ($obat) {
                        // Extract numeric qty from dosage string (e.g., "2 Pcs" -> 2)
                        $qty = (int) filter_var($prescription->dosage, FILTER_SANITIZE_NUMBER_INT) ?: 1;
                        $obat->stok -= $qty;
                        $obat->save();
                    }
                }
            }
        }

        $invoice->appointment->user->notify(new \App\Notifications\PaymentSuccessNotification());

        return back()->with('success', 'Pembayaran lunas! Stok obat otomatis dikurangi dan notifikasi dikirim.');
    }

    public function konfirmasiLunas($id)
    {
        abort_if(auth()->user()->role === 'dokter', 403, 'Akses Ditolak: Dokter tidak boleh memproses pembayaran.');
        $invoice = Invoice::with('appointment.medical_record.prescriptions')->findOrFail($id);
        
        // Update status tagihan jadi lunas
        $invoice->update([
            'status' => 'paid',
            'payment_method' => 'cashier'
        ]);

        // Kurangi stok obat
        $medicalRecord = $invoice->appointment->medical_record ?? null;
        if ($medicalRecord && $medicalRecord->prescriptions) {
            foreach ($medicalRecord->prescriptions as $prescription) {
                if ($prescription->obat_id) {
                    $obat = \App\Models\Obat::find($prescription->obat_id);
                    if ($obat) {
                        // Extract numeric qty from dosage string (e.g., "2 Pcs" -> 2)
                        $qty = (int) filter_var($prescription->dosage, FILTER_SANITIZE_NUMBER_INT) ?: 1;
                        $obat->stok -= $qty;
                        $obat->save();
                    }
                }
            }
        }

        $invoice->appointment->user->notify(new \App\Notifications\PaymentSuccessNotification());

        return back()->with('success', 'Pembayaran lunas! Stok obat otomatis dikurangi dan notifikasi dikirim.');
    }
}