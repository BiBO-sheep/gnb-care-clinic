<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\Appointment;
use App\Models\Invoice;
use App\Models\Prescription;
use App\Models\MedicalRecord;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LaporanBulanan extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-chart-bar';
    protected static ?string $navigationGroup = 'Laporan';
    protected static ?string $navigationLabel = 'Laporan Bulanan';
    protected static ?string $title = 'Laporan Rekapitulasi Klinik';
    protected static ?int $navigationSort = 3;

    protected static string $view = 'filament.pages.laporan-bulanan';

    public $selectedMonth;
    
    // Stats variables
    public $totalPemasukan = 0;
    public $totalBooking = 0;
    public $totalSelesai = 0;
    public $totalObatTerjual = 0;

    // Table data
    public $recentDiagnoses = [];
    public $doctorStats = [];

    public function mount()
    {
        // Default to current month or previous month? User asked for previous month default.
        $this->selectedMonth = Carbon::now()->subMonth()->format('Y-m'); 
        $this->loadData();
    }

    public function updatedSelectedMonth()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $year = substr($this->selectedMonth, 0, 4);
        $month = substr($this->selectedMonth, 5, 2);

        // 1. Pemasukan
        $this->totalPemasukan = Invoice::where('status', 'paid')
                                  ->whereYear('created_at', $year)
                                  ->whereMonth('created_at', $month)
                                  ->sum('grand_total');

        // 2. Total Booking
        $this->totalBooking = Appointment::whereYear('created_at', $year)
                                  ->whereMonth('created_at', $month)
                                  ->count();

        // 3. Pasien Selesai Diperiksa
        $this->totalSelesai = Appointment::where('status', 'selesai')
                                  ->whereYear('created_at', $year)
                                  ->whereMonth('created_at', $month)
                                  ->count();

        // 4. Obat Keluar (Hanya yang dari invoice lunas)
        $this->totalObatTerjual = Prescription::whereHas('medical_record.appointment.invoice', function($q) use ($year, $month) {
                                      $q->where('status', 'paid')
                                        ->whereYear('created_at', $year)
                                        ->whereMonth('created_at', $month);
                                  })->count();

        // 5. Statistik Dokter
        $this->doctorStats = MedicalRecord::with('doctor')
                                ->whereHas('appointment', function($q) use ($year, $month) {
                                    $q->whereYear('created_at', $year)
                                      ->whereMonth('created_at', $month);
                                })
                                ->select('doctor_id', DB::raw('count(*) as total'))
                                ->groupBy('doctor_id')
                                ->get();

        // 6. Diagnosa Terbaru di bulan tersebut
        $this->recentDiagnoses = MedicalRecord::with(['user', 'doctor'])
                                ->whereHas('appointment', function($q) use ($year, $month) {
                                    $q->whereYear('created_at', $year)
                                      ->whereMonth('created_at', $month);
                                })
                                ->orderBy('created_at', 'desc')
                                ->take(10)
                                ->get();
    }
}
