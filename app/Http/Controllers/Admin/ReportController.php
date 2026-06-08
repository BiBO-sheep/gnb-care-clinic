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
        // Parse the requested month, default to current month
        $filterMonth = $request->input('month', Carbon::now()->format('Y-m'));
        $dateParts = explode('-', $filterMonth);
        $selectedYear = $dateParts[0] ?? Carbon::now()->year;
        $selectedMonth = $dateParts[1] ?? Carbon::now()->month;

        // Data Laporan Keuangan
        $paidInvoices = Invoice::with(['user', 'appointment.poli'])
            ->where('status', 'paid')
            ->whereYear('updated_at', $selectedYear)
            ->whereMonth('updated_at', $selectedMonth)
            ->orderBy('updated_at', 'desc')
            ->get();

        // Data Kinerja Dokter
        $doctorPerformance = User::where('role', 'dokter')->get()->map(function($doc) use ($selectedMonth, $selectedYear) {
            $doc->completed_appointments_count = Appointment::where('dokter_id', $doc->id)
                ->where('status', 'selesai')
                ->whereMonth('updated_at', $selectedMonth)
                ->whereYear('updated_at', $selectedYear)
                ->count();
            return $doc;
        });

        // Data Rekap Obat
        $medicinesSold = Prescription::whereHas('medicalRecord.appointment.invoice', function($q) {
                $q->where('status', 'paid');
            })
            ->whereYear('created_at', $selectedYear)
            ->whereMonth('created_at', $selectedMonth)
            ->with(['medicalRecord.appointment.user'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.reports.index', compact('paidInvoices', 'doctorPerformance', 'medicinesSold', 'filterMonth'));
    }

    public function exportPdfFinance(Request $request)
    {
        $filterMonth = $request->input('month', Carbon::now()->format('Y-m'));
        $dateParts = explode('-', $filterMonth);
        $selectedYear = $dateParts[0] ?? Carbon::now()->year;
        $selectedMonth = $dateParts[1] ?? Carbon::now()->month;

        $invoices = Invoice::with(['user', 'appointment.poli'])
            ->where('status', 'paid')
            ->whereYear('updated_at', $selectedYear)
            ->whereMonth('updated_at', $selectedMonth)
            ->orderBy('updated_at', 'desc')
            ->get();

        $pdf = Pdf::loadView('admin.reports.pdf-finance', compact('invoices', 'filterMonth'));
        // download('name.pdf') to automatically download, stream() to view in browser
        return $pdf->download('Laporan_Keuangan_Klinik_'.$filterMonth.'.pdf');
    }

    public function exportPdfMedicine(Request $request)
    {
        $filterMonth = $request->input('month', Carbon::now()->format('Y-m'));
        $dateParts = explode('-', $filterMonth);
        $selectedYear = $dateParts[0] ?? Carbon::now()->year;
        $selectedMonth = $dateParts[1] ?? Carbon::now()->month;

        $medicines = Prescription::whereHas('medicalRecord.appointment.invoice', function($q) {
                $q->where('status', 'paid');
            })
            ->whereYear('created_at', $selectedYear)
            ->whereMonth('created_at', $selectedMonth)
            ->with(['medicalRecord.appointment.user'])
            ->orderBy('created_at', 'desc')
            ->get();

        $pdf = Pdf::loadView('admin.reports.pdf-medicine', compact('medicines', 'filterMonth'));
        return $pdf->download('Laporan_Rekap_Obat_Klinik_'.$filterMonth.'.pdf');
    }
}
