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
        $invoice = Invoice::with([
            'appointment.user', 
            'appointment.poli',
            'appointment.dokter',
            'appointment.medical_record.prescriptions'
        ])
            ->where('appointment_id', $appointmentId)
            ->first();

        if (!$invoice) {
            return response()->json([
                'success' => false,
                'message' => 'Invoice not found'
            ], 404);
        }

        $medicalRecord = $invoice->appointment->medical_record;
        $prescriptions = $medicalRecord ? $medicalRecord->prescriptions : collect();

        // Format daftar obat untuk Flutter
        $medicines = $prescriptions->map(function ($p) {
            return [
                'id'            => $p->id,
                'medicine_name' => $p->medicine_name,
                'dosage'        => $p->dosage,
                'rules'         => $p->rules,
                'price'         => (int) $p->price,
            ];
        })->values();

        // Tentukan status yang tampil di Flutter
        // pending_kasir = dokter sudah isi resep, kasir belum input harga
        // unpaid tapi semua obat masih harga 0 = juga dianggap pending
        $status = $invoice->status;
        $allMedicinesPriced = $prescriptions->isNotEmpty() 
            ? $prescriptions->every(fn($p) => $p->price > 0)
            : true; // Kalau tidak ada resep, tidak perlu harga obat

        if ($status === 'pending_kasir' || ($status === 'unpaid' && $prescriptions->isNotEmpty() && !$allMedicinesPriced)) {
            $displayStatus = 'pending'; // Kasir belum selesai input harga
        } else {
            $displayStatus = $status; // 'unpaid' (siap bayar) atau 'paid'
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id'                => $invoice->id,
                'invoice_number'    => 'INV-' . str_pad($invoice->id, 6, '0', STR_PAD_LEFT),
                'status'            => $displayStatus,
                'total_consultation'=> (int) $invoice->total_consultation,
                'total_medicines'   => (int) $invoice->total_medicines,
                'grand_total'       => (int) $invoice->grand_total,
                'appointment'       => [
                    'id'            => $invoice->appointment->id,
                    'queue_number'  => $invoice->appointment->queue_number,
                    'tanggal'       => $invoice->appointment->tanggal,
                    'poli'          => [
                        'name'      => $invoice->appointment->poli->name ?? '-',
                        'ruangan'   => $invoice->appointment->poli->ruangan ?? '-',
                    ],
                    'user'          => [
                        'name'      => $invoice->appointment->user->name ?? 'Pasien',
                    ],
                    'dokter'        => [
                        'name'      => $invoice->appointment->dokter->name ?? 'Dokter',
                    ],
                    'medical_record' => $medicalRecord ? [
                        'diagnosis'     => $medicalRecord->diagnosis ?? '-',
                        'treatment_plan'=> $medicalRecord->treatment_plan ?? '-',
                        'doctor_notes'  => $medicalRecord->doctor_notes ?? '-',
                    ] : null,
                ],
                'medicines'         => $medicines,
            ]
        ]);
    }
}
