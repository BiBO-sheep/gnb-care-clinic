<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Invoice;
use App\Models\User;
use App\Models\Prescription;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today      = Carbon::today()->toDateString();
        $thisMonth  = Carbon::now()->month;
        $thisYear   = Carbon::now()->year;

        // ── Statistik Hari Ini ───────────────────────────────────────────
        $todayAppointments = Appointment::whereRaw(
            "STR_TO_DATE(tanggal, '%b %e, %Y') = ?", [$today]
        )->count();

        $todayRevenue = Invoice::where('status', 'paid')
            ->whereDate('updated_at', today())
            ->sum('grand_total');

        // ── Statistik Bulan Ini ──────────────────────────────────────────
        $monthRevenue = Invoice::where('status', 'paid')
            ->whereMonth('updated_at', $thisMonth)
            ->whereYear('updated_at', $thisYear)
            ->sum('grand_total');

        $monthConsultation = Invoice::where('status', 'paid')
            ->whereMonth('updated_at', $thisMonth)
            ->whereYear('updated_at', $thisYear)
            ->sum('total_consultation');

        $monthMedicines = Invoice::where('status', 'paid')
            ->whereMonth('updated_at', $thisMonth)
            ->whereYear('updated_at', $thisYear)
            ->sum('total_medicines');

        // ── Total Keseluruhan ────────────────────────────────────────────
        $totalAllRevenue   = Invoice::where('status', 'paid')->sum('grand_total');
        $totalPaidInvoices = Invoice::where('status', 'paid')->count();

        // ── Pending / Actionable ─────────────────────────────────────────
        $pendingKasirCount  = Invoice::where('status', 'pending_kasir')->count();
        $pendingUnpaidCount = Invoice::where('status', 'unpaid')->count();

        // ── Data Pasien ──────────────────────────────────────────────────
        $totalPatients = User::where('role', 'pasien')->count();

        // ── Riwayat Booking Terbaru (50 data) ───────────────────────────
        $recentBookings = Appointment::with(['user', 'poli', 'dokter', 'invoice'])
            ->orderBy('id', 'desc')
            ->limit(50)
            ->get();

        // ── Laporan Keuangan: Invoice yang sudah Paid ────────────────────
        $paidInvoices = Invoice::with(['user', 'appointment.poli'])
            ->where('status', 'paid')
            ->orderBy('updated_at', 'desc')
            ->limit(50)
            ->get();

        // ── Kinerja Dokter (Bulan Ini) ───────────────────────────────────
        $doctorPerformance = User::where('role', 'dokter')
            ->withCount(['appointments as completed_appointments_count' => function ($query) use ($thisMonth, $thisYear) {
                $query->where('status', 'selesai')
                      ->whereMonth('updated_at', $thisMonth)
                      ->whereYear('updated_at', $thisYear);
            }])
            ->get();

        // ── Rekap Obat Terjual (Dari Tagihan Lunas) ──────────────────────
        $medicinesSold = Prescription::whereHas('medical_record.appointment.invoice', function($q) {
                $q->where('status', 'paid');
            })
            ->orderBy('created_at', 'desc')
            ->limit(100)
            ->get();

        return view('admin.dashboard', compact(
            'todayAppointments',
            'todayRevenue',
            'monthRevenue',
            'monthConsultation',
            'monthMedicines',
            'totalAllRevenue',
            'totalPaidInvoices',
            'pendingKasirCount',
            'pendingUnpaidCount',
            'totalPatients',
            'recentBookings',
            'paidInvoices',
            'doctorPerformance',
            'medicinesSold'
        ));
    }

    public function cleanupBugData()
    {
        $todayStr = Carbon::today()->toDateString();
        
        // Cari data yang jadwalnya KURANG DARI hari ini, dan statusnya belum selesai/batal/kadaluarsa
        $appointments = Appointment::with(['invoice', 'medical_record.prescriptions'])
            ->whereRaw("STR_TO_DATE(tanggal, '%b %e, %Y') < ?", [$todayStr])
            ->whereNotIn('status', ['selesai', 'batal', 'kadaluarsa'])
            ->get();

        $deletedCount = 0;
        foreach ($appointments as $apt) {
            if ($apt->invoice) {
                $apt->invoice->delete();
            }
            if ($apt->medical_record) {
                $apt->medical_record->prescriptions()->delete();
                $apt->medical_record->delete();
            }
            $apt->delete();
            $deletedCount++;
        }

        return redirect()->back()->with('success', "Ajaib! Berhasil menghapus permanen {$deletedCount} data bug/masa lalu yang menyangkut!");
    }
}
