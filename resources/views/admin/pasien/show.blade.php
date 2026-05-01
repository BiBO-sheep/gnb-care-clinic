@extends('layouts.admin')

@section('title', 'Rekam Medis - ' . $user->name)
@section('header', 'Rekam Medis Pasien')
@section('subheader', 'Lihat riwayat lengkap pengobatan dan pemeriksaan pasien.')

@section('content')
<div class="space-y-8">
    <!-- Profile Card -->
    <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100 flex flex-col md:flex-row gap-8 items-start md:items-center relative overflow-hidden">
        <div class="absolute -right-20 -top-20 w-64 h-64 bg-primaryLight rounded-full opacity-50 blur-3xl"></div>
        
        <div class="relative z-10 w-24 h-24 rounded-full bg-primary flex items-center justify-center text-white text-4xl font-black shadow-xl shadow-primary/30 border-4 border-white">
            {{ substr($user->name, 0, 1) }}
        </div>
        
        <div class="relative z-10 flex-1">
            <div class="flex flex-wrap items-center gap-3 mb-2">
                <h2 class="text-3xl font-extrabold text-gray-800">{{ $user->name }}</h2>
                <span class="px-3 py-1 rounded-full bg-primaryLight text-primary font-bold text-xs uppercase tracking-tighter">Pasien Tetap</span>
            </div>
            <div class="flex flex-wrap gap-x-6 gap-y-2 text-gray-500 font-body text-sm">
                <span><i class="fa-solid fa-envelope w-5"></i> {{ $user->email }}</span>
                <span><i class="fa-solid fa-phone w-5"></i> {{ $user->phone ?? 'Tidak ada telepon' }}</span>
                <span><i class="fa-solid fa-location-dot w-5"></i> {{ $user->address ?? 'Alamat belum diatur' }}</span>
            </div>
        </div>

        <div class="relative z-10 flex gap-4">
            <div class="bg-gray-50 rounded-2xl p-4 border border-gray-100 text-center min-w-[100px]">
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Gol. Darah</p>
                <p class="text-xl font-black text-primary">{{ $user->blood_type ?? '-' }}</p>
            </div>
            <div class="bg-gray-50 rounded-2xl p-4 border border-gray-100 text-center min-w-[100px]">
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Total Sesi</p>
                <p class="text-xl font-black text-secondary">{{ $user->appointments->count() }}</p>
            </div>
        </div>
    </div>

    <!-- Timeline Riwayat -->
    <div class="space-y-6">
        <h3 class="text-xl font-extrabold text-gray-800 flex items-center">
            <i class="fa-solid fa-clock-rotate-left mr-3 text-primary"></i> Riwayat Pemeriksaan
        </h3>

        @forelse($user->appointments as $app)
        <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 hover:border-primary/30 transition-all">
            <div class="flex flex-wrap justify-between items-start gap-4 mb-6">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-gray-50 flex items-center justify-center text-gray-400 border border-gray-100">
                        <i class="fa-solid fa-calendar-day text-xl"></i>
                    </div>
                    <div>
                        <p class="font-bold text-gray-800 text-lg">{{ \Carbon\Carbon::parse($app->tanggal)->format('d M Y') }}</p>
                        <p class="text-xs text-gray-400 font-body">{{ $app->jam }} • {{ $app->poli->name ?? 'Poli Umum' }}</p>
                    </div>
                </div>
                
                @if($app->status == 'selesai')
                <span class="px-4 py-1.5 rounded-full bg-green-100 text-green-700 font-bold text-xs uppercase">Selesai</span>
                @elseif($app->status == 'scheduled' || $app->status == 'check_in')
                <span class="px-4 py-1.5 rounded-full bg-blue-100 text-blue-700 font-bold text-xs uppercase">Dalam Proses</span>
                @else
                <span class="px-4 py-1.5 rounded-full bg-gray-100 text-gray-500 font-bold text-xs uppercase">{{ $app->status }}</span>
                @endif
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Data Medis -->
                <div class="bg-gray-50 rounded-2xl p-5 border border-gray-100">
                    <p class="text-[10px] font-bold text-primary uppercase tracking-widest mb-4 flex items-center">
                        <i class="fa-solid fa-stethoscope mr-2"></i> Hasil Diagnosa & Resep
                    </p>
                    
                    @if($app->medical_record)
                    <div class="space-y-4">
                        <div>
                            <p class="text-xs font-bold text-gray-400 mb-1">Diagnosis</p>
                            <p class="text-gray-700 font-bold">{{ $app->medical_record->diagnosis }}</p>
                        </div>
                        
                        <div>
                            <p class="text-xs font-bold text-gray-400 mb-2">Resep Obat</p>
                            <div class="space-y-2">
                                @forelse($app->medical_record->prescriptions as $med)
                                <div class="flex justify-between items-center bg-white p-3 rounded-xl border border-gray-100 shadow-sm">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 rounded-lg bg-primary/5 flex items-center justify-center text-primary text-xs mr-3">
                                            <i class="fa-solid fa-pills"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-gray-800 leading-tight">{{ $med->medicine_name }}</p>
                                            <p class="text-[10px] text-gray-400 uppercase tracking-tighter">{{ $med->rules }}</p>
                                        </div>
                                    </div>
                                    <span class="text-xs font-black text-gray-400">x{{ $med->dosage }}</span>
                                </div>
                                @empty
                                <p class="text-xs text-gray-400 italic">Tidak ada resep obat.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                    @else
                    <p class="text-sm text-gray-400 italic py-4">Data rekam medis belum tersedia untuk sesi ini.</p>
                    @endif
                </div>

                <!-- Data Keuangan -->
                <div class="bg-gray-50 rounded-2xl p-5 border border-gray-100">
                    <p class="text-[10px] font-bold text-secondary uppercase tracking-widest mb-4 flex items-center">
                        <i class="fa-solid fa-receipt mr-2"></i> Rincian Biaya & Tagihan
                    </p>
                    
                    @if($app->invoice)
                    <div class="space-y-3">
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-500 font-body">Biaya Konsultasi</span>
                            <span class="font-bold text-gray-700">Rp {{ number_format($app->invoice->total_consultation, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center text-sm pb-3 border-b border-gray-200">
                            <span class="text-gray-500 font-body">Total Biaya Obat</span>
                            <span class="font-bold text-gray-700">Rp {{ number_format($app->invoice->total_medicines, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center pt-1">
                            <span class="text-sm font-bold text-gray-800">Total Tagihan</span>
                            <span class="text-lg font-black text-secondary">Rp {{ number_format($app->invoice->grand_total, 0, ',', '.') }}</span>
                        </div>
                        <div class="mt-4">
                            @if($app->invoice->status == 'paid')
                            <span class="w-full block text-center py-2 rounded-xl bg-green-500 text-white font-bold text-[10px] uppercase shadow-md shadow-green-200">LUNAS - {{ strtoupper($app->invoice->payment_method) }}</span>
                            @else
                            <span class="w-full block text-center py-2 rounded-xl bg-amber-500 text-white font-bold text-[10px] uppercase shadow-md shadow-amber-200">BELUM DIBAYAR</span>
                            @endif
                        </div>
                    </div>
                    @else
                    <p class="text-sm text-gray-400 italic py-4">Data invoice belum tersedia untuk sesi ini.</p>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="bg-white rounded-3xl p-12 shadow-sm border border-gray-100 text-center">
            <i class="fa-solid fa-folder-open text-5xl text-gray-100 mb-4 block"></i>
            <p class="text-gray-400">Belum ada riwayat kunjungan medis untuk pasien ini.</p>
        </div>
        @endforelse
    </div>

    <div class="pt-6">
        <a href="/klinik/pasien" class="text-gray-400 hover:text-primary transition-all font-bold flex items-center gap-2">
            <i class="fa-solid fa-arrow-left"></i> Kembali ke Daftar Pasien
        </a>
    </div>
</div>
@endsection
