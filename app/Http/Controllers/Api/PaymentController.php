<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Midtrans\Config; // ✅ Pastikan ada ini
use Midtrans\Snap;   // ✅ Pastikan ada ini

class PaymentController extends Controller
{
    public function process(Request $request)
    {
        try {
            // 1. Validasi Input
            $request->validate([
                'invoice_id' => 'required',
            ]);

            // 2. Ambil data dengan relasi yang benar (Safety Check)
            $invoice = Invoice::with(['appointment.user'])->find($request->invoice_id);

            if (!$invoice) {
                return response()->json(['message' => 'Invoice not found'], 404);
            }

            // 3. Setup Konfigurasi Midtrans
            Config::$serverKey = env('MIDTRANS_SERVER_KEY');
            Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);
            Config::$isSanitized = true;
            Config::$is3ds = true;

            // 4. Buat Payload (dengan fallback default)
            $params = [
                'transaction_details' => [
                    'order_id' => 'INV-' . $invoice->id . '-' . time(),
                    'gross_amount' => (int) $invoice->grand_total,
                ],
                'customer_details' => [
                    'first_name' => $invoice->appointment->user->name ?? 'Pasien',
                    'email' => $invoice->appointment->user->email ?? 'pasien@klinik.com',
                ],
            ];

            // 5. Create Transaction
            $snapUrl = Snap::createTransaction($params)->redirect_url;

            return response()->json([
                'success' => true,
                'snap_url' => $snapUrl
            ]);

        } catch (\Exception $e) {
            // 6. Log Error ke file storage/logs/laravel.log
            \Log::error('Midtrans Error: ' . $e->getMessage());

            // 7. Kembalikan Response Error Lengkap untuk Debugging
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }
}
