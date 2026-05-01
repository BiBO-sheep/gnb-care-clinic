@extends('layouts.admin')

@section('title', 'Buku Pasien')
@section('header', 'Daftar Pasien')
@section('subheader', 'Lihat dan kelola data pasien yang terdaftar di sistem.')

@section('content')
<div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-6 border-b border-gray-100 flex justify-between items-center">
        <h3 class="font-bold text-lg text-gray-800">Data Pasien ({{ $pasiens->count() }})</h3>
        <div class="relative w-64">
            <i class="fa-solid fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
            <input type="text" placeholder="Cari nama pasien..." class="w-full pl-10 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all">
        </div>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-500 font-body">
            <thead class="text-xs text-gray-400 uppercase bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-4">Pasien</th>
                    <th scope="col" class="px-6 py-4">Kontak</th>
                    <th scope="col" class="px-6 py-4">Tgl Bergabung</th>
                    <th scope="col" class="px-6 py-4">Total Kunjungan</th>
                    <th scope="col" class="px-6 py-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pasiens as $pasien)
                <tr class="bg-white border-b hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold mr-3 shadow-sm">
                                {{ substr($pasien->name, 0, 1) }}
                            </div>
                            <div>
                                <p class="font-bold text-gray-800 leading-tight">{{ $pasien->name }}</p>
                                <p class="text-xs text-gray-400">{{ $pasien->email }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 font-semibold text-gray-600">
                        {{ $pasien->phone ?? '-' }}
                    </td>
                    <td class="px-6 py-4 text-gray-400">
                        {{ $pasien->created_at->format('d M Y') }}
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-primaryLight text-primary font-bold text-xs">
                            <i class="fa-solid fa-calendar-check mr-1.5 opacity-60"></i> {{ $pasien->appointments_count }} Sesi
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <a href="/klinik/pasien/{{ $pasien->id }}" class="bg-white hover:bg-primary hover:text-white border border-primary text-primary font-bold px-4 py-2 rounded-xl text-xs transition-all inline-flex items-center shadow-sm">
                            <i class="fa-solid fa-file-medical mr-2"></i> Rekam Medis
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-gray-400">
                        <i class="fa-solid fa-user-slash text-4xl mb-3 block opacity-20"></i>
                        Belum ada data pasien terdaftar.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
