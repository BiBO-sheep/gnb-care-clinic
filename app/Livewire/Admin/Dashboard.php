<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Appointment;
use App\Models\Invoice;
use App\Models\User;
use Carbon\Carbon;

#[Layout('layouts.admin')]
class Dashboard extends Component
{
    public function cleanupBugData()
    {
        $todayStr = Carbon::today()->toDateString();
        
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

        session()->flash('success', "Ajaib! Berhasil menghapus permanen {$deletedCount} data bug/masa lalu yang menyangkut!");
    }

    public function render()
    {
        $today      = Carbon::today()->toDateString();
        $thisMonth  = Carbon::now()->month;
        $thisYear   = Carbon::now()->year;

        $todayAppointments = Appointment::whereRaw(
            "STR_TO_DATE(tanggal, '%b %e, %Y') = ?", [$today]
        )->count();

        $todayRevenue = Invoice::where('status', 'paid')
            ->whereDate('updated_at', today())
            ->sum('grand_total');

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

        $totalAllRevenue   = Invoice::where('status', 'paid')->sum('grand_total');
        $totalPaidInvoices = Invoice::where('status', 'paid')->count();

        $pendingKasirCount  = Invoice::where('status', 'pending_kasir')->count();
        $pendingUnpaidCount = Invoice::where('status', 'unpaid')->count();

        $totalPatients = User::where('role', 'pasien')->count();

        $recentBookings = Appointment::with(['user', 'poli', 'dokter', 'invoice'])
            ->orderBy('id', 'desc')
            ->limit(50)
            ->get();

        return view('livewire.admin.dashboard', compact(
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
            'recentBookings'
        ));
    }
}
