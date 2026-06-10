@section('title', $isEdit ? 'Edit Obat' : 'Tambah Obat')
@section('header', $isEdit ? 'Edit Data Obat' : 'Tambah Obat Baru')

<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-3xl p-6 md:p-8 shadow-sm border border-gray-100">
        <div class="mb-8 border-b border-gray-100 pb-6">
            <h2 class="text-xl font-extrabold text-gray-800">{{ $isEdit ? 'Edit Data Obat' : 'Tambah Obat Baru' }}</h2>
            <p class="text-sm text-gray-500 mt-1">Isi formulir di bawah untuk {{ $isEdit ? 'memperbarui' : 'menyimpan' }} data obat ke dalam sistem.</p>
        </div>

        <form wire:submit.prevent="save" class="space-y-6">
            
            {{-- Nama Obat --}}
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Nama Obat <span class="text-red-500">*</span></label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i class="fa-solid fa-capsules text-gray-400"></i>
                    </div>
                    <input type="text" wire:model.defer="nama_obat" placeholder="Contoh: Paracetamol 500mg" class="w-full pl-11 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all" required>
                </div>
                @error('nama_obat') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Harga --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Harga (Rp) <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 font-bold text-sm">
                            Rp
                        </div>
                        <input type="number" wire:model.defer="harga" placeholder="0" min="0" class="w-full pl-12 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all" required>
                    </div>
                    @error('harga') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                {{-- Stok --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Stok Awal <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fa-solid fa-boxes-stacked text-gray-400"></i>
                        </div>
                        <input type="number" wire:model.defer="stok" placeholder="0" min="0" class="w-full pl-11 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all" required>
                    </div>
                    @error('stok') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            {{-- Keterangan --}}
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Keterangan / Deskripsi (Opsional)</label>
                <textarea wire:model.defer="keterangan" rows="3" placeholder="Tambahkan catatan khusus tentang obat ini..." class="w-full p-4 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all resize-none"></textarea>
                @error('keterangan') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div class="pt-4 flex flex-col sm:flex-row gap-4">
                <a href="/klinik/obat" wire:navigate class="w-full sm:w-1/3 px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl transition-all text-center">
                    Batal
                </a>
                <button type="submit" class="w-full sm:w-2/3 bg-primary hover:bg-[#004f54] text-white font-bold py-3 rounded-xl transition-all shadow-md shadow-primary/20 flex items-center justify-center gap-2">
                    <i class="fa-solid fa-save"></i> Simpan Data Obat
                </button>
            </div>

        </form>
    </div>
</div>
