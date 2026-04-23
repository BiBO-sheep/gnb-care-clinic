<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    /**
     * Get invoice details by appointment ID.
     */
    public function show($appointmentId)
    {
        $invoice = Invoice::with(['appointment.user', 'appointment.medical_record'])
            ->where('appointment_id', $appointmentId)
            ->first();

        if (!$invoice) {
            return response()->json([
                'success' => false,
                'message' => 'Invoice not found'
            ], 404);
        }

        // Generate a simple invoice number if it doesn't exist in DB
        $invoiceNumber = "INV-" . str_pad($invoice->id, 6, '0', STR_PAD_LEFT);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $invoice->id,
                'invoice_number' => $invoiceNumber,
                'status' => $invoice->status,
                'consultation_fee' => (int) $invoice->total_consultation,
                'medicine_fee' => (int) $invoice->total_medicines,
                'grand_total' => (int) $invoice->grand_total,
                'diagnosis' => $invoice->appointment->medical_record->diagnosis ?? '-',
                'patient_name' => $invoice->appointment->user->name ?? 'Pasien',
                'clinic_name' => 'G&B Care Clinic',
                'medicines' => [] // Prescriptions can be added here if needed later
            ]
        ]);
    }
}
