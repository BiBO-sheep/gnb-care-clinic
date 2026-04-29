@extends('layouts.admin')

@section('title', 'Pembayaran & Kasir')
@section('header', 'Pembayaran')
@section('subheader', 'Kelola tagihan dan konfirmasi pembayaran pasien.')

@section('content')
<div class="space-y-6">
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
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

    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100">
            <h3 class="font-bold text-lg text-gray-800">Daftar Tagihan Belum Lunas</h3>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500 font-body">
                <thead class="text-xs text-gray-400 uppercase bg-gray-50">
                    <tr>
                        <th class="px-6 py-4">No. Invoice</th>
                        <th class="px-6 py-4">Nama Pasien</th>
                        <th class="px-6 py-4">Poli Tujuan</th>
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
                        <td class="px-6 py-4">
                            <span class="text-lg font-extrabold text-gray-900">Rp {{ number_format($inv->grand_total, 0, ',', '.') }}</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <form action="/admin/kasir/{{ $inv->id }}/lunas" method="POST">
                                @csrf
                                <button type="submit" class="bg-white hover:bg-primary hover:text-white border border-primary text-primary font-bold px-4 py-2 rounded-xl text-xs transition-all shadow-sm">
                                    Konfirmasi Lunas
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-400 font-body">
                            Tidak ada tagihan yang tertunda. Semua lunas bos!
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection