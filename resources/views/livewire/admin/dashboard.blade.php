@section('title', 'Dashboard')
@section('header', 'Dashboard')

<div class="space-y-8">

    {{-- ================================================================ --}}
    {{-- BARIS 0: NOTIFIKASI & HEADER --}}
    {{-- ================================================================ --}}
    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl shadow-sm flex items-center gap-3">
        <i class="fa-solid fa-circle-check text-xl"></i>
        <span class="font-bold">{{ session('success') }}</span>
    </div>
    @endif

    <div class="flex justify-between items-center bg-white p-6 rounded-3xl shadow-sm border border-gray-100">
        <div>
            <h2 class="text-xl font-extrabold text-gray-800">Ikhtisar Klinik</h2>
            <p class="text-sm text-gray-500 mt-1">Pantau antrean, pendapatan, dan hapus data bug dengan cepat.</p>
        </div>
        <button type="button" wire:click="cleanupBugData" wire:confirm="PERINGATAN!\nAksi ini akan MENGHAPUS PERMANEN semua data antrean, rekam medis, dan tagihan yang berstatus nyangkut atau yang jadwalnya kurang dari hari ini.\n\nYakin ingin membersihkan database untuk presentasi?" class="bg-red-500 hover:bg-red-600 text-white text-sm font-bold py-2.5 px-5 rounded-xl shadow-md shadow-red-500/20 transition-all flex items-center gap-2">
            <i class="fa-solid fa-trash-can"></i>
            Sapu Bersih Data Bug
        </button>
    </div>

    {{-- ================================================================ --}}
    {{-- BARIS 1: KARTU STATISTIK UTAMA --}}
    {{-- ================================================================ --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6">

        {{-- Pendapatan Bulan Ini --}}
        <div class="relative bg-gradient-to-br from-primary to-[#1a4a9e] rounded-3xl p-6 text-white overflow-hidden shadow-lg shadow-primary/30">
            <div class="absolute -right-4 -top-4 w-24 h-24 rounded-full bg-white/10"></div>
            <div class="absolute -right-2 -bottom-6 w-32 h-32 rounded-full bg-white/5"></div>
            <div class="relative z-10">
                <div class="w-12 h-12 bg-white/20 rounded-2xl flex items-center justify-center mb-4">
                    <i class="fa-solid fa-money-bill-trend-up text-xl"></i>
                </div>
                <p class="text-white/70 font-bold text-xs uppercase tracking-wider mb-1">Pendapatan Bulan Ini</p>
                <p class="text-3xl font-extrabold leading-none">Rp {{ number_format($monthRevenue, 0, ',', '.') }}</p>
                <div class="mt-3 flex items-center gap-3 text-xs text-white/60">
                    <span><i class="fa-solid fa-stethoscope mr-1"></i>Konsul: Rp {{ number_format($monthConsultation, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center gap-3 text-xs text-white/60">
                    <span><i class="fa-solid fa-pills mr-1"></i>Obat: Rp {{ number_format($monthMedicines, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        {{-- Pendapatan Hari Ini --}}
        <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="w-14 h-14 bg-green-50 rounded-2xl flex items-center justify-center text-green-500 text-2xl flex-shrink-0">
                <i class="fa-solid fa-circle-dollar-to-slot"></i>
            </div>
            <div>
                <p class="text-gray-500 font-bold text-xs uppercase tracking-wider">Pendapatan Hari Ini</p>
                <p class="text-2xl font-extrabold text-gray-900">Rp {{ number_format($todayRevenue, 0, ',', '.') }}</p>
                <p class="text-xs text-gray-400 mt-1">{{ now()->format('d M Y') }}</p>
            </div>
        </div>

        {{-- Booking Hari Ini --}}
        <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="w-14 h-14 bg-blue-50 rounded-2xl flex items-center justify-center text-secondary text-2xl flex-shrink-0">
                <i class="fa-solid fa-calendar-day"></i>
            </div>
            <div>
                <p class="text-gray-500 font-bold text-xs uppercase tracking-wider">Booking Hari Ini</p>
                <p class="text-2xl font-extrabold text-gray-900">{{ $todayAppointments }} <span class="text-sm font-medium text-gray-400">Pasien</span></p>
                <p class="text-xs text-gray-400 mt-1">Total pasien: {{ $totalPatients }}</p>
            </div>
        </div>

        {{-- Pending Aksi --}}
        <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="w-14 h-14 bg-orange-50 rounded-2xl flex items-center justify-center text-orange-500 text-2xl flex-shrink-0">
                <i class="fa-solid fa-hourglass-half"></i>
            </div>
            <div>
                <p class="text-gray-500 font-bold text-xs uppercase tracking-wider">Perlu Tindakan</p>
                <p class="text-2xl font-extrabold text-gray-900">{{ $pendingKasirCount + $pendingUnpaidCount }} <span class="text-sm font-medium text-gray-400">Tagihan</span></p>
                <div class="flex gap-3 mt-1">
                    <span class="text-xs text-yellow-600 font-bold"><i class="fa-solid fa-clock mr-1"></i>{{ $pendingKasirCount }} input harga</span>
                </div>
                <div class="flex gap-3">
                    <span class="text-xs text-orange-600 font-bold"><i class="fa-solid fa-receipt mr-1"></i>{{ $pendingUnpaidCount }} belum bayar</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ================================================================ --}}
    {{-- BARIS 2: RINGKASAN TOTAL & QUICK LINKS --}}
    {{-- ================================================================ --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="md:col-span-2 bg-white rounded-3xl p-6 shadow-sm border border-gray-100">
            <h3 class="font-extrabold text-gray-800 text-lg mb-5">Ringkasan Pendapatan Kumulatif</h3>
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-gray-50 rounded-2xl p-4 text-center">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Total Semua Waktu</p>
                    <p class="text-2xl font-extrabold text-primary">Rp {{ number_format($totalAllRevenue, 0, ',', '.') }}</p>
                </div>
                <div class="bg-gray-50 rounded-2xl p-4 text-center">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Invoice Lunas</p>
                    <p class="text-2xl font-extrabold text-green-600">{{ $totalPaidInvoices }} <span class="text-sm font-medium text-gray-400">Transaksi</span></p>
                </div>
                <div class="bg-primaryLight rounded-2xl p-4 text-center">
                    <p class="text-xs font-bold text-primary/60 uppercase tracking-wider mb-2">Jasa Konsultasi (Bln Ini)</p>
                    <p class="text-xl font-extrabold text-primary">Rp {{ number_format($monthConsultation, 0, ',', '.') }}</p>
                </div>
                <div class="bg-blue-50 rounded-2xl p-4 text-center">
                    <p class="text-xs font-bold text-blue-400 uppercase tracking-wider mb-2">Penjualan Obat (Bln Ini)</p>
                    <p class="text-xl font-extrabold text-secondary">Rp {{ number_format($monthMedicines, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100">
            <h3 class="font-extrabold text-gray-800 text-lg mb-5">Akses Cepat</h3>
            <div class="space-y-3">
                <a href="/klinik/queue" wire:navigate class="flex items-center gap-3 p-3 rounded-2xl hover:bg-primaryLight group transition-all">
                    <div class="w-10 h-10 bg-primaryLight group-hover:bg-primary rounded-xl flex items-center justify-center transition-colors">
                        <i class="fa-solid fa-list-ol text-primary group-hover:text-white text-sm transition-colors"></i>
                    </div>
                    <div>
                        <p class="font-bold text-gray-800 text-sm">Monitor Antrean</p>
                        <p class="text-xs text-gray-400">Lihat & kelola antrean</p>
                    </div>
                    <i class="fa-solid fa-chevron-right ml-auto text-gray-300 text-xs"></i>
                </a>
                <a href="/klinik/kasir" wire:navigate class="flex items-center gap-3 p-3 rounded-2xl hover:bg-yellow-50 group transition-all">
                    <div class="w-10 h-10 bg-yellow-50 group-hover:bg-yellow-400 rounded-xl flex items-center justify-center transition-colors">
                        <i class="fa-solid fa-cash-register text-yellow-500 group-hover:text-white text-sm transition-colors"></i>
                    </div>
                    <div>
                        <p class="font-bold text-gray-800 text-sm">Kasir</p>
                        <p class="text-xs text-gray-400">Input harga & konfirmasi</p>
                    </div>
                    <i class="fa-solid fa-chevron-right ml-auto text-gray-300 text-xs"></i>
                </a>
                <a href="/klinik/doctor" wire:navigate class="flex items-center gap-3 p-3 rounded-2xl hover:bg-green-50 group transition-all">
                    <div class="w-10 h-10 bg-green-50 group-hover:bg-green-500 rounded-xl flex items-center justify-center transition-colors">
                        <i class="fa-solid fa-user-doctor text-green-500 group-hover:text-white text-sm transition-colors"></i>
                    </div>
                    <div>
                        <p class="font-bold text-gray-800 text-sm">Ruang Dokter</p>
                        <p class="text-xs text-gray-400">Input resep & diagnosis</p>
                    </div>
                    <i class="fa-solid fa-chevron-right ml-auto text-gray-300 text-xs"></i>
                </a>
                <a href="/klinik/pasien" wire:navigate class="flex items-center gap-3 p-3 rounded-2xl hover:bg-purple-50 group transition-all">
                    <div class="w-10 h-10 bg-purple-50 group-hover:bg-purple-500 rounded-xl flex items-center justify-center transition-colors">
                        <i class="fa-solid fa-address-book text-purple-500 group-hover:text-white text-sm transition-colors"></i>
                    </div>
                    <div>
                        <p class="font-bold text-gray-800 text-sm">Buku Pasien</p>
                        <p class="text-xs text-gray-400">Rekam medis & riwayat</p>
                    </div>
                    <i class="fa-solid fa-chevron-right ml-auto text-gray-300 text-xs"></i>
                </a>
            </div>
        </div>
    </div>

    {{-- ================================================================ --}}
    {{-- BARIS 3: RIWAYAT BOOKING --}}
    {{-- ================================================================ --}}
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100 flex items-center justify-between">
            <div>
                <h3 class="font-extrabold text-lg text-gray-800">Riwayat Booking Pasien</h3>
                <p class="text-xs text-gray-400 mt-1">50 data booking terbaru dari semua waktu.</p>
            </div>
            <span class="bg-primaryLight text-primary text-xs font-bold px-3 py-1 rounded-full">{{ $recentBookings->count() }} data</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500 font-body">
                <thead class="text-xs text-gray-400 uppercase bg-gray-50">
                    <tr>
                        <th class="px-6 py-4">Pasien</th>
                        <th class="px-6 py-4">Poli / Dokter</th>
                        <th class="px-6 py-4">Tanggal</th>
                        <th class="px-6 py-4">No. Antrean</th>
                        <th class="px-6 py-4">Invoice</th>
                        <th class="px-6 py-4">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($recentBookings as $apt)
                    <tr class="bg-white hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($apt->user->name ?? 'P') }}&background=E6F0FA&color=0B2B5E&size=64" class="w-8 h-8 rounded-xl flex-shrink-0">
                                <div>
                                    <p class="font-bold text-gray-800 text-sm">{{ $apt->user->name ?? '-' }}</p>
                                    <p class="text-xs text-gray-400">{{ $apt->user->email ?? '' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <p class="font-semibold text-gray-700 text-sm">{{ $apt->poli->nama ?? 'Poli Umum' }}</p>
                            <p class="text-xs text-gray-400">{{ $apt->dokter->name ?? 'Belum ditentukan' }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-gray-700 font-semibold">{{ $apt->tanggal }}</p>
                            <p class="text-xs text-gray-400">{{ $apt->jam }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <span class="font-bold text-primary text-sm">{{ $apt->queue_number ?? '-' }}</span>
                        </td>
                        <td class="px-6 py-4">
                            @if($apt->invoice)
                                <p class="font-bold text-gray-800 text-sm">Rp {{ number_format($apt->invoice->grand_total, 0, ',', '.') }}</p>
                                @if($apt->invoice->status === 'paid')
                                    <span class="text-xs text-green-600 font-bold"><i class="fa-solid fa-circle-check mr-1"></i>Lunas</span>
                                @elseif($apt->invoice->status === 'unpaid')
                                    <span class="text-xs text-orange-500 font-bold"><i class="fa-solid fa-clock mr-1"></i>Belum bayar</span>
                                @else
                                    <span class="text-xs text-yellow-600 font-bold"><i class="fa-solid fa-hourglass mr-1"></i>Proses kasir</span>
                                @endif
                            @else
                                <span class="text-xs text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $statusMap = [
                                    'pending'     => ['bg-gray-100 text-gray-600', 'Pending'],
                                    'scheduled'   => ['bg-blue-100 text-blue-700', 'Terjadwal'],
                                    'check_in'    => ['bg-yellow-100 text-yellow-700', 'Check-in'],
                                    'pemeriksaan' => ['bg-purple-100 text-purple-700', 'Diperiksa'],
                                    'selesai'     => ['bg-green-100 text-green-700', 'Selesai'],
                                    'batal'       => ['bg-red-100 text-red-700', 'Dibatal'],
                                ];
                                $s = $statusMap[$apt->status] ?? ['bg-gray-100 text-gray-500', $apt->status];
                            @endphp
                            <span class="px-3 py-1 rounded-full text-xs font-bold {{ $s[0] }}">{{ $s[1] }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-400">Belum ada data booking.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
