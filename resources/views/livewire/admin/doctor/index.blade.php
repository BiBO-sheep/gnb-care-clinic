@section('title', 'Ruang Dokter')
@section('header', 'Ruang Dokter')

<div wire:poll.10s class="space-y-8">
    @if(!$isAdmin)
        {{-- VIEW UNTUK DOKTER (Mode Normal) --}}
        @if($activePatient)
        <div class="bg-primaryLight border border-primary/20 rounded-3xl p-6 flex justify-between items-center shadow-sm">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 bg-white rounded-2xl flex items-center justify-center text-primary text-2xl shadow-sm">
                    <i class="fa-solid fa-stethoscope"></i>
                </div>
                <div>
                    <p class="text-primary font-bold text-xs uppercase tracking-widest">Sedang Diperiksa</p>
                    <h3 class="text-xl font-extrabold text-gray-900">{{ $activePatient->user->name }}</h3>
                    <p class="text-sm text-gray-600 mb-2">Antrean: <b>{{ $activePatient->queue_number }}</b></p>
                    <div class="flex items-center gap-2">
                        <span class="bg-blue-100 text-blue-700 text-[10px] px-2 py-1 rounded font-bold uppercase">{{ $activePatient->poli->name ?? 'Poli' }}</span>
                        <span class="bg-gray-100 text-gray-600 text-[10px] px-2 py-1 rounded font-bold"><i class="fa-regular fa-clock mr-1"></i>{{ $activePatient->jam }}</span>
                    </div>
                </div>
            </div>
            <a href="/klinik/doctor/examine/{{ $activePatient->id }}" wire:navigate class="bg-primary text-white px-8 py-3 rounded-xl font-bold hover:bg-[#004f54] transition-all">
                Lanjutkan Input Resep
            </a>
        </div>
        @endif

        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100">
                <h3 class="font-bold text-lg text-gray-800">Pasien Menunggu ({{ collect($waitingPatients)->count() }})</h3>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($waitingPatients as $patient)
                <div class="bg-gray-50 rounded-2xl p-5 border border-gray-100 hover:border-primary/30 transition-all group">
                    <div class="flex justify-between items-start mb-4">
                        <span class="text-2xl font-black text-gray-300 group-hover:text-primary/20 transition-colors">{{ $patient->queue_number }}</span>
                        <span class="bg-white px-3 py-1 rounded-full text-[10px] font-bold text-gray-400 border border-gray-100">{{ $patient->jam }}</span>
                    </div>
                    <h4 class="font-bold text-gray-800 mb-3 text-lg">{{ $patient->user->name }}</h4>
                    <div class="flex flex-wrap gap-1 mb-5">
                        <span class="bg-blue-100 text-blue-700 text-[10px] px-2 py-1 rounded font-bold uppercase">{{ $patient->poli->name ?? 'Poli' }}</span>
                    </div>
                    <a href="/klinik/doctor/examine/{{ $patient->id }}" wire:navigate class="block text-center bg-white border border-gray-200 text-gray-700 py-3 rounded-xl font-bold text-sm hover:bg-primary hover:text-white hover:border-primary transition-all">
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

    @else
        {{-- VIEW UNTUK ADMIN (Dikelompokkan Berdasarkan Poli) --}}
        
        {{-- Gabungkan semua nama poli yang ada pasien aktif atau menunggu --}}
        @php
            $allPolis = collect($activePatients)->keys()->merge(collect($waitingPatients)->keys())->unique();
        @endphp

        @forelse($allPolis as $poliName)
        <div class="bg-white rounded-3xl shadow-sm border border-gray-200 overflow-hidden mb-8">
            <div class="bg-primaryLight/50 p-4 border-b border-gray-200 flex items-center gap-3">
                <i class="fa-solid fa-hospital-user text-primary text-xl"></i>
                <h3 class="font-black text-lg text-primary uppercase">{{ $poliName }}</h3>
            </div>
            
            <div class="p-6">
                {{-- Pasien Sedang Diperiksa di Poli ini --}}
                @if(isset($activePatients[$poliName]) && $activePatients[$poliName]->count() > 0)
                    <h4 class="font-bold text-gray-600 mb-4 text-sm"><i class="fa-solid fa-user-doctor mr-2"></i> Sedang Diperiksa</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
                        @foreach($activePatients[$poliName] as $activePatient)
                        <div class="bg-green-50 border border-green-200 rounded-2xl p-5 flex flex-col relative overflow-hidden shadow-sm">
                            <div class="absolute right-0 top-0 w-16 h-16 bg-green-200 rounded-bl-full opacity-30"></div>
                            <div class="flex justify-between items-start mb-2 relative z-10">
                                <span class="text-xl font-black text-green-700">{{ $activePatient->queue_number }}</span>
                                <span class="bg-white px-2 py-1 rounded text-[10px] font-bold text-green-600 border border-green-100"><i class="fa-regular fa-clock"></i> {{ $activePatient->jam }}</span>
                            </div>
                            <h4 class="font-bold text-gray-800 mb-1 relative z-10">{{ $activePatient->user->name }}</h4>
                            <p class="text-xs font-bold text-green-700 mb-4 relative z-10">👨‍⚕️ dr. {{ $activePatient->dokter->name ?? '-' }}</p>
                            
                            <div class="mt-auto relative z-10">
                                <a href="/klinik/doctor/examine/{{ $activePatient->id }}" wire:navigate class="block text-center bg-green-600 text-white py-2 rounded-xl font-bold text-xs hover:bg-green-700 transition-all">
                                    Lihat Detail / Status
                                </a>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @endif

                {{-- Pasien Menunggu di Poli ini --}}
                <h4 class="font-bold text-gray-600 mb-4 text-sm"><i class="fa-solid fa-users mr-2"></i> Menunggu Antrean ({{ isset($waitingPatients[$poliName]) ? $waitingPatients[$poliName]->count() : 0 }})</h4>
                @if(isset($waitingPatients[$poliName]) && $waitingPatients[$poliName]->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        @foreach($waitingPatients[$poliName] as $patient)
                        <div class="bg-gray-50 rounded-2xl p-4 border border-gray-200 flex flex-col">
                            <div class="flex justify-between items-start mb-2">
                                <span class="text-lg font-black text-gray-500">{{ $patient->queue_number }}</span>
                                <span class="bg-white px-2 py-1 rounded text-[10px] font-bold text-gray-500 border border-gray-200">{{ $patient->jam }}</span>
                            </div>
                            <h4 class="font-bold text-gray-800 text-sm mb-1">{{ $patient->user->name }}</h4>
                            <p class="text-[10px] font-bold text-gray-500 mb-4">👨‍⚕️ dr. {{ $patient->dokter->name ?? '-' }}</p>
                            
                            <div class="mt-auto">
                                <a href="/klinik/doctor/examine/{{ $patient->id }}" wire:navigate class="block text-center bg-white border border-gray-300 text-gray-700 py-2 rounded-xl font-bold text-[10px] hover:bg-gray-100 transition-all">
                                    Lihat Data Pasien
                                </a>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-400 italic">Tidak ada antrean menunggu.</p>
                @endif
            </div>
        </div>
        @empty
            <div class="bg-white rounded-3xl p-12 shadow-sm border border-gray-100 text-center">
                <i class="fa-solid fa-bed-pulse text-5xl text-gray-300 mb-4"></i>
                <h3 class="text-xl font-bold text-gray-400 mb-2">Klinik Terpantau Sepi</h3>
                <p class="text-gray-400">Belum ada pasien yang masuk atau menunggu di poli manapun saat ini.</p>
            </div>
        @endforelse

    @endif
</div>
