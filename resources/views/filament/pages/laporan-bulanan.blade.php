<x-filament-panels::page>
    <div class="space-y-6">
        
        <!-- Filter Bulan -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center justify-between dark:bg-gray-800 dark:border-gray-700">
            <div>
                <h2 class="text-lg font-bold text-gray-800 dark:text-gray-200">Pilih Bulan Laporan</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">Pilih bulan untuk melihat rekapitulasi data operasional klinik.</p>
            </div>
            <div class="w-64">
                <input type="month" wire:model.live="selectedMonth" 
                       class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
            </div>
        </div>

        <!-- Kartu Statistik -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 dark:bg-gray-800 dark:border-gray-700">
                <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Pemasukan</div>
                <div class="mt-2 text-3xl font-bold text-green-600">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</div>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 dark:bg-gray-800 dark:border-gray-700">
                <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Booking</div>
                <div class="mt-2 text-3xl font-bold text-primary">{{ $totalBooking }}</div>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 dark:bg-gray-800 dark:border-gray-700">
                <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Pasien Selesai Diperiksa</div>
                <div class="mt-2 text-3xl font-bold text-blue-600">{{ $totalSelesai }}</div>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 dark:bg-gray-800 dark:border-gray-700">
                <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Jenis Obat Terjual</div>
                <div class="mt-2 text-3xl font-bold text-orange-600">{{ $totalObatTerjual }}</div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Kinerja Dokter -->
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 dark:bg-gray-800 dark:border-gray-700">
                <h3 class="font-bold text-gray-800 dark:text-gray-200 mb-4">Kinerja Dokter</h3>
                <ul class="space-y-4">
                    @forelse($doctorStats as $stat)
                        <li class="flex justify-between items-center pb-4 border-b last:border-0 dark:border-gray-700">
                            <span class="text-gray-600 dark:text-gray-300 font-medium">{{ $stat->doctor->name ?? 'Dokter' }}</span>
                            <span class="bg-primary/10 text-primary px-3 py-1 rounded-full text-sm font-bold">
                                {{ $stat->total }} Pasien
                            </span>
                        </li>
                    @empty
                        <li class="text-gray-500 text-sm italic">Belum ada data di bulan ini.</li>
                    @endforelse
                </ul>
            </div>

            <!-- Rekam Medis & Diagnosa -->
            <div class="lg:col-span-2 bg-white p-6 rounded-xl shadow-sm border border-gray-100 dark:bg-gray-800 dark:border-gray-700">
                <h3 class="font-bold text-gray-800 dark:text-gray-200 mb-4">10 Diagnosa Terbaru (Bulan Ini)</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-300">
                            <tr>
                                <th class="px-4 py-3 rounded-l-lg">Tanggal</th>
                                <th class="px-4 py-3">Pasien</th>
                                <th class="px-4 py-3">Dokter</th>
                                <th class="px-4 py-3 rounded-r-lg">Diagnosis</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentDiagnoses as $mr)
                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                    <td class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                        {{ $mr->created_at->format('d M Y') }}
                                    </td>
                                    <td class="px-4 py-3">{{ $mr->user->name ?? '-' }}</td>
                                    <td class="px-4 py-3">{{ $mr->doctor->name ?? '-' }}</td>
                                    <td class="px-4 py-3 font-semibold text-primary">{{ $mr->diagnosis }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-8 text-center italic">Tidak ada rekam medis di bulan ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</x-filament-panels::page>
