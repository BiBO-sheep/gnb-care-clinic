<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-3xl p-6 md:p-8 shadow-sm border border-gray-100">
        
        {{-- Header Pasien --}}
        <div class="flex items-center gap-4 mb-8 pb-6 border-b border-gray-100">
            <img src="https://ui-avatars.com/api/?name={{ urlencode($appointment->user->name) }}&background=E0F7F7&color=006A6A" class="w-14 h-14 rounded-2xl flex-shrink-0">
            <div>
                <h2 class="text-xl font-extrabold text-gray-900">{{ $appointment->user->name }}</h2>
                <p class="text-gray-500 text-sm">Nomor Antrean: <b class="text-primary">{{ $appointment->queue_number }}</b></p>
                <p class="text-gray-400 text-xs">Poli: {{ $appointment->poli->name ?? '-' }}</p>
            </div>
        </div>

        @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-6 py-4 rounded-2xl font-body text-sm flex items-center gap-3 mb-6">
            <i class="fa-solid fa-circle-xmark text-red-500 text-lg"></i> {{ session('error') }}
        </div>
        @endif

        <form wire:submit.prevent="simpanResep" class="space-y-6">
            @if(auth()->user()->role === 'admin')
            <div class="bg-red-50 border border-red-200 rounded-xl p-4 text-sm text-red-600 font-bold mb-4">
                <i class="fa-solid fa-lock mr-2"></i> Mode Hanya Baca (Read-Only). Hanya dokter yang berhak mengisi rekam medis.
            </div>
            @endif

            {{-- Keluhan Pasien --}}
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">
                    <i class="fa-solid fa-head-side-cough text-primary mr-1"></i> Keluhan Pasien
                </label>
                <textarea wire:model.defer="keluhan" rows="2" class="w-full bg-gray-50 border border-gray-200 rounded-xl p-4 outline-none focus:ring-2 focus:ring-primary/20 transition-all text-sm resize-none" placeholder="Tuliskan keluhan utama pasien..." required {{ auth()->user()->role === 'admin' ? 'readonly' : '' }}></textarea>
            </div>

            {{-- Diagnosis --}}
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">
                    <i class="fa-solid fa-stethoscope text-primary mr-1"></i> Diagnosis Penyakit
                </label>
                <textarea wire:model.defer="diagnosis" rows="2" class="w-full bg-gray-50 border border-gray-200 rounded-xl p-4 outline-none focus:ring-2 focus:ring-primary/20 transition-all text-sm resize-none" placeholder="Tuliskan diagnosis penyakit pasien..." required {{ auth()->user()->role === 'admin' ? 'readonly' : '' }}></textarea>
            </div>

            {{-- Penanganan / Tindakan --}}
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">
                    <i class="fa-solid fa-hand-holding-medical text-primary mr-1"></i> Penanganan / Tindakan Medis
                </label>
                <textarea wire:model.defer="tindakan" rows="2" class="w-full bg-gray-50 border border-gray-200 rounded-xl p-4 outline-none focus:ring-2 focus:ring-primary/20 transition-all text-sm resize-none" placeholder="Tindakan medis yang dilakukan (misal: penjahitan luka, dll)..." required {{ auth()->user()->role === 'admin' ? 'readonly' : '' }}></textarea>
            </div>

            {{-- Resep Obat (Tanpa Harga — Kasir yang isi) --}}
            <div class="p-5 bg-orange-50/50 rounded-2xl border border-orange-100">
                <div class="flex justify-between items-center mb-4">
                    <div>
                        <p class="font-bold text-gray-900 italic text-sm">
                            <i class="fa-solid fa-pills text-secondary mr-2"></i> Daftar Resep Obat
                        </p>
                        <p class="text-xs text-gray-400 mt-0.5">Harga obat akan diisi oleh bagian Kasir.</p>
                    </div>
                    @if(auth()->user()->role !== 'admin')
                    <button type="button" wire:click="addMedicine" class="text-xs bg-secondary text-white px-4 py-1.5 rounded-lg hover:bg-opacity-90 transition-all font-bold flex-shrink-0">
                        <i class="fa-solid fa-plus mr-1"></i> Tambah Obat
                    </button>
                    @endif
                </div>

                {{-- Header Kolom --}}
                <div class="hidden md:grid grid-cols-12 gap-2 mb-2 px-1">
                    <p class="col-span-5 text-xs font-bold text-gray-400 uppercase">Nama Obat</p>
                    <p class="col-span-2 text-xs font-bold text-gray-400 uppercase">Qty</p>
                    <p class="col-span-4 text-xs font-bold text-gray-400 uppercase">Dosis / Aturan Pakai</p>
                    <p class="col-span-1"></p>
                </div>

                <div class="space-y-2">
                    @foreach($medicines as $index => $medicine)
                    <div class="grid grid-cols-12 gap-2 medicine-row items-center">
                        <div class="col-span-12 md:col-span-5">
                            <select wire:model.defer="medicines.{{ $index }}.obat_id" class="w-full bg-white border border-gray-200 rounded-xl p-2.5 text-sm outline-none focus:ring-2 focus:ring-secondary/20" required {{ auth()->user()->role === 'admin' ? 'disabled' : '' }}>
                                <option value="">Pilih Obat...</option>
                                @foreach($obats as $obat)
                                    <option value="{{ $obat->id }}">{{ $obat->nama_obat }} (Stok: {{ $obat->stok }})</option>
                                @endforeach
                            </select>
                        </div>
                        <input type="number" wire:model.defer="medicines.{{ $index }}.qty" placeholder="Qty" min="1" class="col-span-4 md:col-span-2 bg-white border border-gray-200 rounded-xl p-2.5 text-sm outline-none focus:ring-2 focus:ring-secondary/20" required {{ auth()->user()->role === 'admin' ? 'readonly' : '' }}>
                        <input type="text" wire:model.defer="medicines.{{ $index }}.rules" placeholder="Contoh: 3x1 sesudah makan" class="col-span-7 md:col-span-4 bg-white border border-gray-200 rounded-xl p-2.5 text-sm outline-none focus:ring-2 focus:ring-secondary/20" required {{ auth()->user()->role === 'admin' ? 'readonly' : '' }}>
                        <div class="col-span-1 flex justify-end">
                            @if(count($medicines) > 1)
                            <button type="button" wire:click="removeMedicine({{ $index }})" class="w-8 h-8 bg-red-100 text-red-400 rounded-lg hover:bg-red-200 transition-all flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid fa-trash text-xs"></i>
                            </button>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Catatan untuk Kasir --}}
            <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 text-xs text-blue-600">
                <i class="fa-solid fa-circle-info mr-1"></i>
                <b>Alur selanjutnya:</b> Setelah Anda klik "Selesai", resep ini akan diteruskan ke bagian <b>Kasir</b> untuk penghitungan harga obat dan penyelesaian tagihan pasien.
            </div>

            <div class="pt-2">
                @if(auth()->user()->role === 'admin')
                <button type="button" onclick="history.back()" class="w-full bg-gray-500 text-white py-4 rounded-2xl font-bold text-base shadow-lg shadow-gray-500/30 hover:bg-gray-600 transition-all flex items-center justify-center gap-3">
                    <i class="fa-solid fa-arrow-left"></i> Kembali
                </button>
                @else
                <button type="submit" class="w-full bg-primary text-white py-4 rounded-2xl font-bold text-base shadow-lg shadow-primary/30 hover:bg-[#004f54] transition-all flex items-center justify-center gap-3">
                    <i class="fa-solid fa-paper-plane"></i> Selesai & Kirim ke Kasir
                </button>
                @endif
            </div>
        </form>
    </div>
</div>
