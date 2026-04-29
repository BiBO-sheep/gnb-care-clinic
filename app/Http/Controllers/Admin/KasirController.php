<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;

class KasirController extends Controller
{
    public function index()
    {
        // Ambil semua tagihan yang belum dibayar, urutkan dari yang terbaru
        $invoices = Invoice::with(['user', 'appointment.poli'])
                    ->where('status', 'unpaid')
                    ->orderBy('created_at', 'desc')
                    ->get();

        // Hitung total uang yang nunggu dibayar hari ini
        $totalTagihan = Invoice::where('status', 'unpaid')->sum('grand_total');
        $jumlahInvoice = $invoices->count();

        return view('admin.kasir', compact('invoices', 'totalTagihan', 'jumlahInvoice'));
    }

    public function konfirmasiLunas($id)
    {
        $invoice = Invoice::findOrFail($id);
        
        // Ubah status invoice jadi lunas via kasir
        $invoice->update([
            'status' => 'paid',
            'payment_method' => 'cashier'
        ]);

        return back()->with('success', 'Pembayaran atas nama ' . $invoice->user->name . ' Berhasil Dikonfirmasi Lunas!');
    }
}