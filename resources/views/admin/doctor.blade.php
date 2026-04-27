@extends('layouts.admin')

@section('title', 'Ruang Pemeriksaan Dokter')
@section('header', 'Ruang Dokter')
@section('subheader', 'Lakukan pemeriksaan dan buat resep digital untuk pasien.')

@section('content')
@if(!$activePatient)
    <div class="flex flex-col items-center justify-center h-[60vh] bg-white rounded-3xl border border-gray-100 shadow-sm">
        <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-6">
            <i class="fa-solid fa-mug-hot text-4xl text-gray-300"></i>
        </div>
        <h2 class="text-2xl font-extrabold text-gray-800 mb-2">Belum Ada Pasien</h2>
        <p class="text-gray-500 font-body text-center max-w-md">Silakan istirahat sejenak. Belum ada pasien yang dialihkan ke ruang pemeriksaan Anda oleh resepsionis.</p>
    </div>
@else
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <div class="space-y-6">
            <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100">
                <div class="text-center mb-6 border-b border-gray-100 pb-6">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($activePatient->user->name ?? 'P') }}&background=E0F7F7&color=006A6A" class="w-24 h-24 rounded-full mx-auto mb-4 border-4 border-white shadow-lg">
                    <h2 class="text-xl font-bold text-gray-900">{{ $activePatient->user->name ?? 'Nama Pasien' }}</h2>
                    <p class="text-sm font-body text-gray-500">Antrean: <span class="font-bold text-primary">{{ $activePatient->queue_number }}</span></p>
                </div>
                
                <div class="space-y-4">
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Keluhan Awal</p>
                        <p class="text-sm text-gray-800 font-body bg-gray-50 p-3 rounded-xl border border-gray-100">
                            {{ $activePatient->keluhan ?? 'Pasien tidak menuliskan keluhan spesifik di aplikasi.' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Rekam Medis Terakhir</p>
                        <p class="text-sm font-bold text-secondary">
                            <i class="fa-solid fa-file-medical mr-1"></i> Belum ada catatan.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="lg:col-span-2">
            <form action="/admin/appointment/{{ $activePatient->id }}/prescribe" method="POST" class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100">
                @csrf
                <div class="flex items-center gap-3 mb-8">
                    <div class="w-10 h-10 bg-primaryLight rounded-xl flex items-center justify-center text-primary">
                        <i class="fa-solid fa-user-doctor text-xl"></i>
                    </div>
                    <h2 class="text-2xl font-extrabold text-gray-800">Catatan Medis & Resep</h2>
                </div>

                <div class="mb-8">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Diagnosis Dokter</label>
                    <textarea name="diagnosis" rows="3" class="w-full bg-gray-50 border border-gray-200 text-gray-900 rounded-xl focus:ring-primary focus:border-primary p-4 outline-none font-body transition-colors" placeholder="Ketikkan hasil pemeriksaan dan diagnosis penyakit di sini..." required></textarea>
                </div>

                <div class="mb-8 p-6 bg-orange-50/50 rounded-2xl border border-orange-100">
                    <div class="flex justify-between items-center mb-4">
                        <label class="block text-sm font-bold text-gray-900"><i class="fa-solid fa-pills text-secondary mr-2"></i> Resep Obat Digital</label>
                        <button type="button" class="text-xs font-bold text-secondary hover:text-[#e06b40]"><i class="fa-solid fa-plus"></i> Tambah Obat</button>
                    </div>
                    
                    <div class="flex gap-4 mb-4">
                        <div class="flex-1">
                            <input type="text" name="medicines[0][name]" placeholder="Nama Obat (mis: Paracetamol 500mg)" class="w-full bg-white border border-gray-200 rounded-xl p-3 text-sm focus:ring-secondary focus:border-secondary outline-none font-body" required>
                        </div>
                        <div class="w-32">
                            <input type="number" name="medicines[0][qty]" placeholder="Jml" class="w-full bg-white border border-gray-200 rounded-xl p-3 text-sm focus:ring-secondary focus:border-secondary outline-none font-body" required>
                        </div>
                        <div class="flex-1">
                            <input type="text" name="medicines[0][rules]" placeholder="Aturan (mis: 3x1 Sesudah Makan)" class="w-full bg-white border border-gray-200 rounded-xl p-3 text-sm focus:ring-secondary focus:border-secondary outline-none font-body" required>
                        </div>
                    </div>
                    
                    <div class="flex gap-4">
                        <div class="flex-1">
                            <input type="text" name="medicines[1][name]" placeholder="Nama Obat (mis: Amoxicillin)" class="w-full bg-white border border-gray-200 rounded-xl p-3 text-sm focus:ring-secondary focus:border-secondary outline-none font-body">
                        </div>
                        <div class="w-32">
                            <input type="number" name="medicines[1][qty]" placeholder="Jml" class="w-full bg-white border border-gray-200 rounded-xl p-3 text-sm focus:ring-secondary focus:border-secondary outline-none font-body">
                        </div>
                        <div class="flex-1">
                            <input type="text" name="medicines[1][rules]" placeholder="Aturan (mis: Habiskan)" class="w-full bg-white border border-gray-200 rounded-xl p-3 text-sm focus:ring-secondary focus:border-secondary outline-none font-body">
                        </div>
                    </div>
                </div>

                <div class="mb-10">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Biaya Tindakan Medis (Rp)</label>
                    <input type="number" name="consultation_fee" value="150000" class="w-full bg-gray-50 border border-gray-200 text-gray-900 rounded-xl focus:ring-primary focus:border-primary p-4 outline-none font-bold text-lg" required>
                    <p class="text-xs text-gray-500 mt-2 font-body">Biaya ini akan otomatis masuk ke tagihan aplikasi pasien (Payment History).</p>
                </div>

                <div class="flex justify-end pt-6 border-t border-gray-100">
                    <button type="submit" class="bg-primary hover:bg-[#004f54] text-white px-10 py-4 rounded-2xl font-bold shadow-xl shadow-primary/30 transition-all flex items-center gap-3 text-lg">
                        <i class="fa-solid fa-paper-plane"></i> Selesai & Kirim Tagihan
                    </button>
                </div>
            </form>
        </div>
    </div>
@endif
@endsection