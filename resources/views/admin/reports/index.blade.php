@extends('layouts.admin')

@section('title', 'Laporan Klinik')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex justify-between items-center bg-white p-6 rounded-3xl shadow-sm border border-gray-100">
        <div>
            <h2 class="text-xl font-extrabold text-gray-800">Laporan Lengkap & Cetak PDF</h2>
            <p class="text-sm text-gray-500 mt-1">Lihat dan unduh laporan detail klinik.</p>
        </div>
        <form action="{{ route('reports.index') }}" method="GET" class="flex items-center gap-3">
            <label for="month" class="text-sm font-bold text-gray-600">Bulan Laporan:</label>
            <input type="month" id="month" name="month" value="{{ $filterMonth }}" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-primary focus:border-primary">
            <button type="submit" class="bg-gray-800 hover:bg-gray-900 text-white font-bold py-2 px-4 rounded-lg text-sm transition-colors">
                Terapkan
            </button>
        </form>
    </div>

    {{-- TABS NAVIGATION --}}
    <div class="border-b border-gray-200">
        <ul class="flex flex-wrap -mb-px text-sm font-medium text-center text-gray-500" id="reportTabs" role="tablist">
            <li class="mr-2" role="presentation">
                <button class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 aria-selected:text-primary aria-selected:border-primary aria-selected:font-bold" id="finance-tab" data-tabs-target="#finance" type="button" role="tab" aria-controls="finance" aria-selected="true" onclick="switchTab('finance')">
                    <i class="fa-solid fa-money-bill-wave mr-2"></i> Laporan Keuangan
                </button>
            </li>
            <li class="mr-2" role="presentation">
                <button class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 aria-selected:text-primary aria-selected:border-primary aria-selected:font-bold" id="medicine-tab" data-tabs-target="#medicine" type="button" role="tab" aria-controls="medicine" aria-selected="false" onclick="switchTab('medicine')">
                    <i class="fa-solid fa-pills mr-2"></i> Rekap Obat Keluar
                </button>
            </li>
            <li class="mr-2" role="presentation">
                <button class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 aria-selected:text-primary aria-selected:border-primary aria-selected:font-bold" id="doctor-tab" data-tabs-target="#doctor" type="button" role="tab" aria-controls="doctor" aria-selected="false" onclick="switchTab('doctor')">
                    <i class="fa-solid fa-user-doctor mr-2"></i> Kinerja Dokter
                </button>
            </li>
        </ul>
    </div>

    {{-- TAB CONTENTS --}}
    <div id="tabContents">
        {{-- TAB 1: KEUANGAN --}}
        <div class="tab-pane" id="finance" role="tabpanel" aria-labelledby="finance-tab">
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                    <div>
                        <h3 class="font-extrabold text-lg text-gray-800">Laporan Transaksi Lunas</h3>
                        <p class="text-xs text-gray-400 mt-1">Total {{ $paidInvoices->count() }} data transaksi yang telah diverifikasi.</p>
                    </div>
                    <a href="{{ route('reports.export.finance', ['month' => $filterMonth]) }}" target="_blank" class="bg-primary hover:bg-primary-dark text-white font-bold py-2 px-4 rounded-xl text-sm transition-colors flex items-center gap-2">
                        <i class="fa-solid fa-download"></i> Download PDF
                    </a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500 font-body">
                        <thead class="text-xs text-gray-400 uppercase bg-gray-50">
                            <tr>
                                <th class="px-6 py-4">No. Invoice</th>
                                <th class="px-6 py-4">Pasien</th>
                                <th class="px-6 py-4">Jasa Dokter</th>
                                <th class="px-6 py-4">Obat-obatan</th>
                                <th class="px-6 py-4">Total</th>
                                <th class="px-6 py-4">Tgl Lunas</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($paidInvoices as $inv)
                            <tr class="bg-white hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4"><span class="font-bold text-primary block">INV-{{ $inv->id }}</span></td>
                                <td class="px-6 py-4"><p class="font-semibold text-gray-800">{{ $inv->user->name ?? '-' }}</p></td>
                                <td class="px-6 py-4 text-gray-700">Rp {{ number_format($inv->total_consultation, 0, ',', '.') }}</td>
                                <td class="px-6 py-4 text-gray-700">Rp {{ number_format($inv->total_medicines, 0, ',', '.') }}</td>
                                <td class="px-6 py-4"><span class="text-base font-extrabold text-gray-900">Rp {{ number_format($inv->grand_total, 0, ',', '.') }}</span></td>
                                <td class="px-6 py-4 text-gray-500 text-xs">{{ $inv->updated_at->format('d M Y') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-400">Belum ada transaksi.</td>
                            </tr>
                            @endforelse
                        </tbody>
                        @if($paidInvoices->count() > 0)
                        <tfoot class="bg-primary-light border-t-2 border-primary/20">
                            <tr>
                                <td colspan="2" class="px-6 py-4 font-extrabold text-primary text-sm uppercase tracking-wider text-right">Total Keseluruhan</td>
                                <td class="px-6 py-4 font-extrabold text-primary">Rp {{ number_format($paidInvoices->sum('total_consultation'), 0, ',', '.') }}</td>
                                <td class="px-6 py-4 font-extrabold text-primary">Rp {{ number_format($paidInvoices->sum('total_medicines'), 0, ',', '.') }}</td>
                                <td class="px-6 py-4 font-extrabold text-primary text-base">Rp {{ number_format($paidInvoices->sum('grand_total'), 0, ',', '.') }}</td>
                                <td></td>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>

        {{-- TAB 2: OBAT --}}
        <div class="tab-pane hidden" id="medicine" role="tabpanel" aria-labelledby="medicine-tab">
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                    <div>
                        <h3 class="font-extrabold text-lg text-gray-800">Rekap Inventaris Obat Keluar</h3>
                        <p class="text-xs text-gray-400 mt-1">Daftar obat yang berhasil diresepkan dan dibayar.</p>
                    </div>
                    <a href="{{ route('reports.export.medicine', ['month' => $filterMonth]) }}" target="_blank" class="bg-primary hover:bg-primary-dark text-white font-bold py-2 px-4 rounded-xl text-sm transition-colors flex items-center gap-2">
                        <i class="fa-solid fa-download"></i> Download PDF
                    </a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500 font-body">
                        <thead class="text-xs text-gray-400 uppercase bg-gray-50">
                            <tr>
                                <th class="px-6 py-4">Tanggal Keluar</th>
                                <th class="px-6 py-4">Nama Obat</th>
                                <th class="px-6 py-4">Dosis / Jumlah</th>
                                <th class="px-6 py-4">Total Harga</th>
                                <th class="px-6 py-4">Pasien</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($medicinesSold as $med)
                            <tr class="bg-white hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 text-xs font-medium text-gray-500">{{ $med->created_at->format('d M Y') }}</td>
                                <td class="px-6 py-4"><span class="font-bold text-gray-800">{{ $med->medicine_name }}</span></td>
                                <td class="px-6 py-4 text-gray-600">{{ $med->dosage }}</td>
                                <td class="px-6 py-4"><span class="font-extrabold text-primary">Rp {{ number_format($med->price, 0, ',', '.') }}</span></td>
                                <td class="px-6 py-4 text-gray-600 text-xs">{{ $med->medicalRecord->appointment->user->name ?? '-' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-400">Belum ada data obat terjual.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- TAB 3: DOKTER --}}
        <div class="tab-pane hidden" id="doctor" role="tabpanel" aria-labelledby="doctor-tab">
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                    <div>
                        <h3 class="font-extrabold text-lg text-gray-800">Kinerja Dokter Bulan Ini</h3>
                        <p class="text-xs text-gray-400 mt-1">Jumlah pasien yang berhasil ditangani.</p>
                    </div>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse($doctorPerformance as $doc)
                    <div class="border border-gray-100 rounded-2xl p-4 flex items-center gap-4">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($doc->name) }}&background=E6F0FA&color=0B2B5E" class="w-14 h-14 rounded-xl">
                        <div>
                            <h4 class="font-bold text-gray-800 text-sm">{{ $doc->name }}</h4>
                            <p class="text-xs text-gray-500">{{ $doc->email }}</p>
                            <div class="mt-2 flex items-center gap-2">
                                <span class="bg-green-100 text-green-700 text-xs font-bold px-2 py-1 rounded-lg"><i class="fa-solid fa-check-double mr-1"></i>{{ $doc->completed_appointments_count }} Pasien Selesai</span>
                            </div>
                        </div>
                    </div>
                    @empty
                    <p class="text-sm text-gray-400 col-span-full">Belum ada data dokter.</p>
                    @endforelse
                </div>
            </div>
        </div>

    </div>

</div>

<script>
    function switchTab(tabId) {
        // Hide all panes
        document.querySelectorAll('.tab-pane').forEach(el => el.classList.add('hidden'));
        // Show target pane
        document.getElementById(tabId).classList.remove('hidden');
        
        // Update tab buttons state
        document.querySelectorAll('[role="tab"]').forEach(el => {
            el.setAttribute('aria-selected', 'false');
        });
        document.getElementById(tabId + '-tab').setAttribute('aria-selected', 'true');
    }
</script>
@endsection
