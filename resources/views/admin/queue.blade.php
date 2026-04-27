@extends('layouts.admin')

@section('title', 'Monitor Antrean')
@section('header', 'Monitor Antrean')
@section('subheader', 'Kelola panggilan pasien untuk hari ini.')

@section('content')
<meta http-equiv="refresh" content="10">

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    
    <div class="lg:col-span-2 space-y-8">
        
        <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100 relative overflow-hidden">
            <div class="absolute -right-10 -top-10 w-40 h-40 bg-primaryLight rounded-full opacity-50 blur-2xl"></div>
            
            <div class="flex justify-between items-start relative z-10">
                <div>
                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-green-100 text-green-700 font-bold text-xs mb-4">
                        <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                        @if($nowServing) Sedang Melayani @else Antrean Kosong @endif
                    </span>
                    <h2 class="text-gray-500 font-bold text-sm uppercase tracking-wider mb-1">Nomor Antrean</h2>
                    <p class="text-7xl font-extrabold text-primary mb-6">
                        {{ $nowServing ? $nowServing->queue_number : '--' }}
                    </p>
                    
                    @if($nowServing)
                    <div class="space-y-2">
                        <p class="text-lg font-bold text-gray-800"><i class="fa-solid fa-user text-gray-400 w-6"></i> {{ $nowServing->user->name ?? 'Pasien' }}</p>
                        <p class="text-sm font-body text-gray-500"><i class="fa-solid fa-stethoscope text-gray-400 w-6"></i> {{ $nowServing->poli->name ?? 'Poli' }} (Dr. {{ $nowServing->dokter->name ?? 'Dokter' }})</p>
                    </div>
                    @endif
                </div>
                
                @if($nowServing)
                <div class="flex flex-col gap-4">
                    <button class="bg-primary hover:bg-[#004f54] text-white px-8 py-4 rounded-2xl font-bold shadow-lg shadow-primary/30 transition-all flex items-center justify-center gap-3">
                        <i class="fa-solid fa-bullhorn text-xl"></i> Panggil Ulang
                    </button>
                    <form action="/admin/appointment/{{ $nowServing->id }}/progress" method="POST">
                        @csrf
                        <button type="submit" class="bg-white hover:bg-gray-50 text-green-600 border border-green-200 px-8 py-4 rounded-2xl font-bold shadow-sm transition-all flex items-center justify-center gap-3 w-full">
                            <i class="fa-solid fa-check text-xl"></i> Pasien Masuk
                        </button>
                    </form>
                </div>
                @endif
            </div>
        </div>

        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                <h3 class="font-bold text-lg text-gray-800">Daftar Tunggu ({{ $waitingList->count() }})</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500 font-body">
                    <thead class="text-xs text-gray-400 uppercase bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-4">No. Antrean</th>
                            <th scope="col" class="px-6 py-4">Nama Pasien</th>
                            <th scope="col" class="px-6 py-4">Tujuan</th>
                            <th scope="col" class="px-6 py-4">Jam</th>
                            <th scope="col" class="px-6 py-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($waitingList as $queue)
                        <tr class="bg-white border-b hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 font-bold text-gray-900 text-base">{{ $queue->queue_number }}</td>
                            <td class="px-6 py-4 font-semibold text-gray-800">{{ $queue->user->name ?? 'Pasien' }}</td>
                            <td class="px-6 py-4">{{ $queue->poli->name ?? 'Poli Umum' }}</td>
                            <td class="px-6 py-4">{{ $queue->jam }}</td>
                            <td class="px-6 py-4">
                                <form action="/admin/appointment/{{ $queue->id }}/call" method="POST">
                                    @csrf
                                    <button type="submit" class="text-primary hover:text-white border border-primary hover:bg-primary font-bold rounded-lg text-xs px-3 py-2 text-center transition-all">
                                        Panggil
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-400">Belum ada pasien dalam antrean.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="space-y-6">
        <div class="bg-primary rounded-3xl p-6 shadow-lg shadow-primary/20 text-white">
            <h3 class="font-bold text-white mb-6">Ringkasan Hari Ini</h3>
            <div class="space-y-4">
                <div class="flex justify-between items-center border-b border-white/10 pb-3">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center"><i class="fa-solid fa-users"></i></div>
                        <span class="font-body text-sm text-primaryLight">Total Pasien</span>
                    </div>
                    <span class="font-bold text-xl">{{ $totalHariIni }}</span>
                </div>
                <div class="flex justify-between items-center border-b border-white/10 pb-3">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center"><i class="fa-solid fa-check-double"></i></div>
                        <span class="font-body text-sm text-primaryLight">Selesai Diperiksa</span>
                    </div>
                    <span class="font-bold text-xl">{{ $selesai }}</span>
                </div>
                <div class="flex justify-between items-center pb-1">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center"><i class="fa-solid fa-clock"></i></div>
                        <span class="font-body text-sm text-primaryLight">Sisa Antrean</span>
                    </div>
                    <span class="font-bold text-xl text-secondary">{{ $waitingList->count() }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection