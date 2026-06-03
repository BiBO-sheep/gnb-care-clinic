<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Invoice;
use App\Models\User;
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
            'paidInvoices'
        ));
    }
}
