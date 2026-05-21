@extends('layouts.admin')

@section('title', 'Pembayaran & Kasir')
@section('header', 'Pembayaran')
@section('subheader', 'Kelola tagihan dan konfirmasi pembayaran pasien.')

@section('content')
<div class="space-y-6">

    {{-- Statistik --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="w-14 h-14 bg-yellow-50 rounded-2xl flex items-center justify-center text-yellow-500 text-2xl">
                <i class="fa-solid fa-clock"></i>
            </div>
            <div>
                <p class="text-gray-500 font-bold text-xs uppercase tracking-wider">Menunggu Harga Obat</p>
                <p class="text-3xl font-extrabold text-gray-900">{{ $pendingKasir->count() }} <span class="text-sm font-medium text-gray-400">Pasien</span></p>
            </div>
        </div>
        <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="w-14 h-14 bg-orange-50 rounded-2xl flex items-center justify-center text-secondary text-2xl">
                <i class="fa-solid fa-file-invoice-dollar"></i>
            </div>
            <div>
                <p class="text-gray-500 font-bold text-xs uppercase tracking-wider">Menunggu Pembayaran</p>
                <p class="text-3xl font-extrabold text-gray-900">{{ $jumlahInvoice }} <span class="text-sm font-medium text-gray-400">Tagihan</span></p>
            </div>
        </div>
        <div class="bg-primary rounded-3xl p-6 shadow-lg shadow-primary/20 flex items-center gap-4 text-white">
            <div class="w-14 h-14 bg-white/20 rounded-2xl flex items-center justify-center text-white text-2xl">
                <i class="fa-solid fa-money-bill-trend-up"></i>
            </div>
            <div>
                <p class="text-white/70 font-bold text-xs uppercase tracking-wider">Potensi Pendapatan</p>
                <p class="text-3xl font-extrabold text-white">Rp {{ number_format($totalTagihan, 0, ',', '.') }}</p>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 px-6 py-4 rounded-2xl font-body text-sm flex items-center gap-3">
        <i class="fa-solid fa-circle-check text-green-500 text-lg"></i> {{ session('success') }}
    </div>
    @endif

    {{-- ================================================================ --}}
    {{-- SECTION 1: MENUNGGU INPUT HARGA OBAT DARI KASIR --}}
    {{-- ================================================================ --}}
    @if($pendingKasir->count() > 0)
    <div class="bg-yellow-50 rounded-3xl shadow-sm border border-yellow-200 overflow-hidden">
        <div class="p-6 border-b border-yellow-200 flex items-center gap-3">
            <div class="w-8 h-8 bg-yellow-400 rounded-xl flex items-center justify-center">
                <i class="fa-solid fa-clock text-white text-sm"></i>
            </div>
            <div>
                <h3 class="font-bold text-lg text-gray-800">Menunggu Input Harga Obat</h3>
                <p class="text-xs text-gray-500">Dokter sudah selesai memeriksa. Silakan masukkan harga masing-masing obat.</p>
            </div>
        </div>

        <div class="p-6 space-y-6">
            @foreach($pendingKasir as $inv)
            @php
                $medRec = $inv->appointment->medical_record ?? null;
                $prescriptions = $medRec ? $medRec->prescriptions : collect();
            @endphp
            <div class="bg-white rounded-2xl p-5 border border-yellow-100 shadow-sm">
                {{-- Header Pasien --}}
                <div class="flex items-start justify-between mb-4 pb-4 border-b border-gray-100">
                    <div class="flex items-center gap-3">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($inv->user->name) }}&background=E0F7F7&color=006A6A" class="w-10 h-10 rounded-xl flex-shrink-0">
                        <div>
                            <p class="font-bold text-gray-900">{{ $inv->user->name }}</p>
                            <p class="text-xs text-gray-400">{{ $inv->appointment->poli->name ?? '-' }} • INV-{{ $inv->id }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-gray-400">Jasa Dokter</p>
                        <p class="font-extrabold text-primary">Rp {{ number_format($inv->total_consultation, 0, ',', '.') }}</p>
                    </div>
                </div>

                {{-- Diagnosis --}}
                @if($medRec)
                <div class="mb-4 bg-blue-50 rounded-xl p-3">
                    <p class="text-xs font-bold text-blue-700 mb-1">Diagnosis:</p>
                    <p class="text-sm text-blue-800">{{ $medRec->diagnosis ?? '-' }}</p>
                </div>
                @endif

                {{-- Form Input Harga Obat --}}
                <form action="/klinik/kasir/{{ $inv->id }}/harga-obat" method="POST">
                    @csrf
                    <p class="text-sm font-bold text-gray-700 mb-3">
                        <i class="fa-solid fa-pills text-secondary mr-1"></i> Daftar Resep Obat
                    </p>

                    @if($prescriptions->count() > 0)
                    <div class="space-y-2 mb-4">
                        @foreach($prescriptions as $pres)
                        <div class="grid grid-cols-12 gap-2 items-center bg-gray-50 rounded-xl p-3">
                            <div class="col-span-12 md:col-span-5">
                                <p class="font-bold text-gray-800 text-sm">{{ $pres->medicine_name }}</p>
                                <p class="text-xs text-gray-400">{{ $pres->dosage }} · {{ $pres->rules }}</p>
                            </div>
                            <div class="col-span-12 md:col-span-4">
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs">Rp</span>
                                    <input type="number" name="prices[{{ $pres->id }}]" placeholder="Harga satuan" min="0" value="{{ $pres->price > 0 ? $pres->price : '' }}" class="w-full bg-white border border-gray-200 rounded-xl pl-8 pr-3 py-2 text-sm outline-none focus:ring-2 focus:ring-primary/20" required>
                                </div>
                            </div>
                            <div class="col-span-12 md:col-span-3 text-right">
                                <p class="text-xs text-gray-400">per satuan</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <p class="text-sm text-gray-400 italic mb-4">Tidak ada resep obat untuk pasien ini.</p>
                    @endif

                    <button type="submit" class="w-full bg-yellow-400 hover:bg-yellow-500 text-yellow-900 font-bold py-3 rounded-xl transition-all text-sm flex items-center justify-center gap-2">
                        <i class="fa-solid fa-calculator"></i> Simpan Harga & Buat Tagihan
                    </button>
                </form>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- ================================================================ --}}
    {{-- SECTION 2: DAFTAR TAGIHAN SIAP BAYAR --}}
    {{-- ================================================================ --}}
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100">
            <h3 class="font-bold text-lg text-gray-800">Daftar Tagihan Siap Bayar</h3>
            <p class="text-xs text-gray-400 mt-1">Pasien dapat melakukan pembayaran di kasir atau via aplikasi.</p>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500 font-body">
                <thead class="text-xs text-gray-400 uppercase bg-gray-50">
                    <tr>
                        <th class="px-6 py-4">No. Invoice</th>
                        <th class="px-6 py-4">Nama Pasien</th>
                        <th class="px-6 py-4">Poli Tujuan</th>
                        <th class="px-6 py-4">Jasa Dokter</th>
                        <th class="px-6 py-4">Obat-obatan</th>
                        <th class="px-6 py-4">Total Tagihan</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoices as $inv)
                    <tr class="bg-white border-b hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <span class="font-bold text-primary block">INV-{{ $inv->id }}</span>
                            <span class="text-xs text-gray-400">{{ $inv->created_at->format('d M Y, H:i') }}</span>
                        </td>
                        <td class="px-6 py-4 font-bold text-gray-800 text-base">
                            {{ $inv->user->name }}
                        </td>
                        <td class="px-6 py-4 text-gray-600">
                            {{ $inv->appointment->poli->name ?? 'Poli Umum' }}
                        </td>
                        <td class="px-6 py-4 text-gray-700">
                            Rp {{ number_format($inv->total_consultation, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 text-gray-700">
                            Rp {{ number_format($inv->total_medicines, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-lg font-extrabold text-gray-900">Rp {{ number_format($inv->grand_total, 0, ',', '.') }}</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <form action="/klinik/kasir/{{ $inv->id }}/lunas" method="POST">
                                @csrf
                                <button type="submit" class="bg-white hover:bg-primary hover:text-white border border-primary text-primary font-bold px-4 py-2 rounded-xl text-xs transition-all shadow-sm">
                                    Konfirmasi Lunas
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-400 font-body">
                            Tidak ada tagihan yang siap bayar saat ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection