@extends('layouts.admin')

@section('title', 'Tambah Obat')
@section('header', 'Tambah Obat')
@section('subheader', 'Tambahkan data master obat baru ke sistem.')

@section('content')
<div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden max-w-2xl">
    <div class="p-6 border-b border-gray-100">
        <h3 class="font-bold text-lg text-gray-800">Form Tambah Obat</h3>
    </div>
    
    <div class="p-6">
        @if ($errors->any())
            <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.obat.store') }}" method="POST" class="space-y-5">
            @csrf
            
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Nama Obat</label>
                <input type="text" name="nama_obat" value="{{ old('nama_obat') }}" required class="w-full bg-white border border-gray-200 rounded-xl px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-primary/20">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Harga (Rp)</label>
                    <input type="number" name="harga" value="{{ old('harga') }}" min="0" required class="w-full bg-white border border-gray-200 rounded-xl px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-primary/20">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Stok Awal</label>
                    <input type="number" name="stok" value="{{ old('stok') }}" min="0" required class="w-full bg-white border border-gray-200 rounded-xl px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-primary/20">
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Keterangan (Opsional)</label>
                <textarea name="keterangan" rows="3" class="w-full bg-white border border-gray-200 rounded-xl px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-primary/20">{{ old('keterangan') }}</textarea>
            </div>

            <div class="pt-4 flex gap-3">
                <a href="{{ route('admin.obat.index') }}" class="px-5 py-2.5 text-sm font-bold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-xl transition-colors">Batal</a>
                <button type="submit" class="px-5 py-2.5 text-sm font-bold text-white bg-primary hover:bg-primary-dark rounded-xl transition-colors">Simpan Obat</button>
            </div>
        </form>
    </div>
</div>
@endsection
