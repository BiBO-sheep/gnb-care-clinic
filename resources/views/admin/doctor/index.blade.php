@extends('layouts.admin')

@section('title', 'Dashboard Dokter')
@section('header', 'Ruang Dokter')
@section('subheader', 'Lihat pasien yang sudah dipanggil dan siap diperiksa.')

@section('content')
<div class="space-y-8">
    @if($activePatient)
    <div class="bg-primaryLight border border-primary/20 rounded-3xl p-6 flex justify-between items-center shadow-sm">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 bg-white rounded-2xl flex items-center justify-center text-primary text-2xl shadow-sm">
                <i class="fa-solid fa-stethoscope"></i>
            </div>
            <div>
                <p class="text-primary font-bold text-xs uppercase tracking-widest">Sedang Diperiksa</p>
                <h3 class="text-xl font-extrabold text-gray-900">{{ $activePatient->user->name }}</h3>
                <p class="text-sm text-gray-600">Antrean: <b>{{ $activePatient->queue_number }}</b></p>
            </div>
        </div>
        <a href="/admin/doctor/examine/{{ $activePatient->id }}" class="bg-primary text-white px-8 py-3 rounded-xl font-bold hover:bg-[#004f54] transition-all">
            Lanjutkan Input Resep
        </a>
    </div>
    @endif

    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100">
            <h3 class="font-bold text-lg text-gray-800">Pasien Menunggu ({{ $waitingPatients->count() }})</h3>
        </div>
        <div class="p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($waitingPatients as $patient)
            <div class="bg-gray-50 rounded-2xl p-5 border border-gray-100 hover:border-primary/30 transition-all group">
                <div class="flex justify-between items-start mb-4">
                    <span class="text-2xl font-black text-gray-300 group-hover:text-primary/20 transition-colors">{{ $patient->queue_number }}</span>
                    <span class="bg-white px-3 py-1 rounded-full text-[10px] font-bold text-gray-400 border border-gray-100">{{ $patient->jam }}</span>
                </div>
                <h4 class="font-bold text-gray-800 mb-5 text-lg">{{ $patient->user->name }}</h4>
                <a href="/admin/doctor/examine/{{ $patient->id }}" class="block text-center bg-white border border-gray-200 text-gray-700 py-3 rounded-xl font-bold text-sm hover:bg-primary hover:text-white hover:border-primary transition-all">
                    Mulai Pemeriksaan
                </a>
            </div>
            @empty
            <div class="col-span-full py-12 text-center text-gray-400">
                <i class="fa-solid fa-mug-hot text-4xl mb-3"></i>
                <p>Belum ada pasien yang masuk. Waktunya ngopi dulu, dok!</p>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection