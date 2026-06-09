@extends('layouts.admin')

@section('title', 'Manajemen Obat')
@section('header', 'Data Obat')
@section('subheader', 'Kelola daftar harga dan stok obat klinik.')

@section('content')
<div class="space-y-6">

    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 px-6 py-4 rounded-2xl font-body text-sm flex items-center gap-3">
        <i class="fa-solid fa-circle-check text-green-500 text-lg"></i> {{ session('success') }}
    </div>
    @endif

    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h3 class="font-bold text-lg text-gray-800">Daftar Master Obat</h3>
                <p class="text-xs text-gray-400 mt-1">Gunakan fitur ini untuk menambah atau memperbarui stok obat.</p>
            </div>
            
            <div class="flex items-center gap-3">
                <form method="GET" action="{{ route('admin.obat.index') }}" class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama obat..." class="pl-10 pr-4 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    <i class="fa-solid fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                </form>
                <a href="{{ route('admin.obat.create') }}" class="bg-primary hover:bg-primary-dark text-white font-bold px-4 py-2 rounded-xl text-sm transition-all shadow-sm flex items-center gap-2">
                    <i class="fa-solid fa-plus"></i> Tambah Obat
                </a>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500 font-body">
                <thead class="text-xs text-gray-400 uppercase bg-gray-50">
                    <tr>
                        <th class="px-6 py-4">Nama Obat</th>
                        <th class="px-6 py-4">Harga</th>
                        <th class="px-6 py-4">Sisa Stok</th>
                        <th class="px-6 py-4">Keterangan</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($obats as $obat)
                    <tr class="bg-white border-b hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <span class="font-bold text-gray-800 block">{{ $obat->nama_obat }}</span>
                        </td>
                        <td class="px-6 py-4 font-bold text-primary text-base">
                            Rp {{ number_format($obat->harga, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4">
                            @if($obat->stok > 10)
                                <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full">{{ $obat->stok }} Pcs</span>
                            @elseif($obat->stok > 0)
                                <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded-full">{{ $obat->stok }} Pcs</span>
                            @else
                                <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded-full">Habis</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-gray-500">
                            {{ Str::limit($obat->keterangan ?? '-', 30) }}
                        </td>
                        <td class="px-6 py-4 flex justify-center gap-2">
                            <a href="{{ route('admin.obat.edit', $obat->id) }}" class="bg-blue-50 hover:bg-blue-100 text-blue-600 px-3 py-1.5 rounded-lg text-xs font-bold transition-colors">
                                <i class="fa-solid fa-edit"></i> Edit
                            </a>
                            <form action="{{ route('admin.obat.destroy', $obat->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus obat ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-50 hover:bg-red-100 text-red-600 px-3 py-1.5 rounded-lg text-xs font-bold transition-colors">
                                    <i class="fa-solid fa-trash"></i> Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-400 font-body">
                            Tidak ada data obat.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-100">
            {{ $obats->links() }}
        </div>
    </div>
</div>
@endsection
