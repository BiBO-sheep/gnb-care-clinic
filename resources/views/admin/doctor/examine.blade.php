@extends('layouts.admin')

@section('title', 'Pemeriksaan Pasien')
@section('header', 'Input Rekam Medis')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100">
        <div class="flex items-center gap-4 mb-8 pb-6 border-b border-gray-100">
            <img src="https://ui-avatars.com/api/?name={{ urlencode($appointment->user->name) }}&background=E0F7F7&color=006A6A" class="w-16 h-16 rounded-2xl">
            <div>
                <h2 class="text-2xl font-extrabold text-gray-900">{{ $appointment->user->name }}</h2>
                <p class="text-gray-500 font-body">Nomor Antrean: <b>{{ $appointment->queue_number }}</b></p>
            </div>
        </div>

        <form action="/admin/appointment/{{ $appointment->id }}/prescribe" method="POST" class="space-y-6">
            @csrf
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Diagnosis Penyakit</label>
                <textarea name="diagnosis" rows="3" class="w-full bg-gray-50 border border-gray-200 rounded-xl p-4 outline-none focus:ring-2 focus:ring-primary/20 transition-all" placeholder="Tuliskan diagnosis..." required></textarea>
            </div>

            <div class="p-6 bg-orange-50/50 rounded-2xl border border-orange-100">
                <p class="font-bold text-gray-900 mb-4 italic text-sm"><i class="fa-solid fa-pills text-secondary mr-2"></i> Resep Obat Digital</p>
                <div class="flex gap-4">
                    <input type="text" name="medicines[0][name]" placeholder="Nama Obat" class="flex-1 bg-white border border-gray-200 rounded-xl p-3 text-sm outline-none" required>
                    <input type="number" name="medicines[0][qty]" placeholder="Qty" class="w-20 bg-white border border-gray-200 rounded-xl p-3 text-sm outline-none" required>
                    <input type="text" name="medicines[0][rules]" placeholder="Dosis (3x1)" class="flex-1 bg-white border border-gray-200 rounded-xl p-3 text-sm outline-none" required>
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Biaya Konsultasi (Rp)</label>
                <input type="number" name="consultation_fee" value="150000" class="w-full bg-gray-50 border border-gray-200 rounded-xl p-4 font-bold text-lg outline-none">
            </div>

            <div class="pt-4">
                <button type="submit" class="w-full bg-primary text-white py-4 rounded-2xl font-bold text-lg shadow-lg shadow-primary/30 hover:bg-[#004f54] transition-all flex items-center justify-center gap-3">
                    <i class="fa-solid fa-paper-plane"></i> Selesai & Kirim ke Pasien
                </button>
            </div>
        </form>
    </div>
</div>
@endsection