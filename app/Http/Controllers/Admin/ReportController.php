<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Prescription;
use App\Models\User;
use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $thisMonth = Carbon::now()->month;
        $thisYear = Carbon::now()->year;

        // Data Laporan Keuangan
        $paidInvoices = Invoice::with(['user', 'appointment.poli'])
            ->where('status', 'paid')
            ->orderBy('updated_at', 'desc')
            ->get();

        // Data Kinerja Dokter
        $doctorPerformance = User::where('role', 'dokter')->get()->map(function($doc) use ($thisMonth, $thisYear) {
            $doc->completed_appointments_count = Appointment::where('dokter_id', $doc->id)
                ->where('status', 'selesai')
                ->whereMonth('updated_at', $thisMonth)
                ->whereYear('updated_at', $thisYear)
                ->count();
            return $doc;
        });

        // Data Rekap Obat
        $medicinesSold = Prescription::whereHas('medicalRecord.appointment.invoice', function($q) {
                $q->where('status', 'paid');
            })
            ->with(['medicalRecord.appointment.user'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.reports.index', compact('paidInvoices', 'doctorPerformance', 'medicinesSold'));
    }

    public function exportPdfFinance()
    {
        $invoices = Invoice::with(['user', 'appointment.poli'])
            ->where('status', 'paid')
            ->orderBy('updated_at', 'desc')
            ->get();

        $pdf = Pdf::loadView('admin.reports.pdf-finance', compact('invoices'));
        // download('name.pdf') to automatically download, stream() to view in browser
        return $pdf->download('Laporan_Keuangan_Klinik.pdf');
    }

    public function exportPdfMedicine()
    {
        $medicines = Prescription::whereHas('medicalRecord.appointment.invoice', function($q) {
                $q->where('status', 'paid');
            })
            ->with(['medicalRecord.appointment.user'])
            ->orderBy('created_at', 'desc')
            ->get();

        $pdf = Pdf::loadView('admin.reports.pdf-medicine', compact('medicines'));
        return $pdf->download('Laporan_Rekap_Obat_Klinik.pdf');
    }
}
