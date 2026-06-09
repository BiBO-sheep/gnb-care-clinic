@extends('layouts.admin')

@section('title', 'Pemeriksaan Pasien')
@section('header', 'Input Rekam Medis')

@section('content')
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

        <form action="/klinik/appointment/{{ $appointment->id }}/prescribe" method="POST" class="space-y-6">
            @csrf

            {{-- Keluhan Pasien --}}
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">
                    <i class="fa-solid fa-head-side-cough text-primary mr-1"></i> Keluhan Pasien
                </label>
                <textarea name="keluhan" rows="2" class="w-full bg-gray-50 border border-gray-200 rounded-xl p-4 outline-none focus:ring-2 focus:ring-primary/20 transition-all text-sm resize-none" placeholder="Tuliskan keluhan utama pasien..." required></textarea>
            </div>

            {{-- Diagnosis --}}
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">
                    <i class="fa-solid fa-stethoscope text-primary mr-1"></i> Diagnosis Penyakit
                </label>
                <textarea name="diagnosis" rows="2" class="w-full bg-gray-50 border border-gray-200 rounded-xl p-4 outline-none focus:ring-2 focus:ring-primary/20 transition-all text-sm resize-none" placeholder="Tuliskan diagnosis penyakit pasien..." required></textarea>
            </div>

            {{-- Penanganan / Tindakan --}}
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">
                    <i class="fa-solid fa-hand-holding-medical text-primary mr-1"></i> Penanganan / Tindakan Medis
                </label>
                <textarea name="tindakan" rows="2" class="w-full bg-gray-50 border border-gray-200 rounded-xl p-4 outline-none focus:ring-2 focus:ring-primary/20 transition-all text-sm resize-none" placeholder="Tindakan medis yang dilakukan (misal: penjahitan luka, dll)..." required></textarea>
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
                    <button type="button" id="addMedicineBtn" class="text-xs bg-secondary text-white px-4 py-1.5 rounded-lg hover:bg-opacity-90 transition-all font-bold flex-shrink-0">
                        <i class="fa-solid fa-plus mr-1"></i> Tambah Obat
                    </button>
                </div>

                {{-- Header Kolom --}}
                <div class="hidden md:grid grid-cols-12 gap-2 mb-2 px-1">
                    <p class="col-span-5 text-xs font-bold text-gray-400 uppercase">Nama Obat</p>
                    <p class="col-span-2 text-xs font-bold text-gray-400 uppercase">Qty</p>
                    <p class="col-span-4 text-xs font-bold text-gray-400 uppercase">Dosis / Aturan Pakai</p>
                    <p class="col-span-1"></p>
                </div>

                <div id="medicineContainer" class="space-y-2">
                    <div class="grid grid-cols-12 gap-2 medicine-row items-center">
                        <div class="col-span-12 md:col-span-5">
                            <select name="medicines[0][obat_id]" class="select2-obat w-full bg-white border border-gray-200 rounded-xl p-2.5 text-sm outline-none focus:ring-2 focus:ring-secondary/20" required>
                                <option value="">Ketik/Pilih Obat...</option>
                                @foreach($obats as $obat)
                                    <option value="{{ $obat->id }}">{{ $obat->nama_obat }} (Stok: {{ $obat->stok }})</option>
                                @endforeach
                            </select>
                        </div>
                        <input type="number" name="medicines[0][qty]" placeholder="Qty" min="1" class="col-span-4 md:col-span-2 bg-white border border-gray-200 rounded-xl p-2.5 text-sm outline-none focus:ring-2 focus:ring-secondary/20" required>
                        <input type="text" name="medicines[0][rules]" placeholder="Contoh: 3x1 sesudah makan" class="col-span-8 md:col-span-4 bg-white border border-gray-200 rounded-xl p-2.5 text-sm outline-none focus:ring-2 focus:ring-secondary/20" required>
                        <div class="col-span-12 md:col-span-1 flex justify-end">
                            {{-- Baris pertama tidak bisa dihapus --}}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Catatan untuk Kasir --}}
            <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 text-xs text-blue-600">
                <i class="fa-solid fa-circle-info mr-1"></i>
                <b>Alur selanjutnya:</b> Setelah Anda klik "Selesai", resep ini akan diteruskan ke bagian <b>Kasir</b> untuk penghitungan harga obat dan penyelesaian tagihan pasien.
            </div>

            <div class="pt-2">
                <button type="submit" class="w-full bg-primary text-white py-4 rounded-2xl font-bold text-base shadow-lg shadow-primary/30 hover:bg-[#004f54] transition-all flex items-center justify-center gap-3">
                    <i class="fa-solid fa-paper-plane"></i> Selesai & Kirim ke Kasir
                </button>
            </div>
        </form>
    </div>
</div>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<style>
    .select2-container .select2-selection--single {
        height: 48px; /* Lebih tinggi agar nampak jelas */
        border-radius: 0.75rem;
        border-color: #e5e7eb;
        display: flex;
        align-items: center;
        font-size: 15px; /* Font lebih besar */
        font-weight: 500;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 48px;
        padding-left: 16px; /* Padding lebih lega */
        color: #374151;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 46px;
    }
    /* Membesarkan ukuran dropdown list-nya juga */
    .select2-results__option {
        padding: 10px 16px;
        font-size: 15px;
    }
</style>
<script>
    $(document).ready(function() {
        $('.select2-obat').select2({
            width: '100%',
            placeholder: "Ketik/Pilih Obat..."
        });
    });

    let medicineCount = 1;
    document.getElementById('addMedicineBtn').addEventListener('click', function() {
        const container = document.getElementById('medicineContainer');
        const html = `
            <div class="grid grid-cols-12 gap-2 medicine-row items-center">
                <div class="col-span-12 md:col-span-5">
                    <select name="medicines[${medicineCount}][obat_id]" class="select2-obat w-full bg-white border border-gray-200 rounded-xl p-2.5 text-sm outline-none focus:ring-2 focus:ring-secondary/20" required>
                        <option value="">Ketik/Pilih Obat...</option>
                        @foreach($obats as $obat)
                            <option value="{{ $obat->id }}">{{ $obat->nama_obat }} (Stok: {{ $obat->stok }})</option>
                        @endforeach
                    </select>
                </div>
                <input type="number" name="medicines[${medicineCount}][qty]" placeholder="Qty" min="1" class="col-span-4 md:col-span-2 bg-white border border-gray-200 rounded-xl p-2.5 text-sm outline-none focus:ring-2 focus:ring-secondary/20" required>
                <input type="text" name="medicines[${medicineCount}][rules]" placeholder="Contoh: 3x1 sesudah makan" class="col-span-7 md:col-span-4 bg-white border border-gray-200 rounded-xl p-2.5 text-sm outline-none focus:ring-2 focus:ring-secondary/20" required>
                <div class="col-span-1 flex justify-end">
                    <button type="button" class="w-8 h-8 bg-red-100 text-red-400 rounded-lg hover:bg-red-200 transition-all flex items-center justify-center flex-shrink-0" onclick="this.closest('.medicine-row').remove()">
                        <i class="fa-solid fa-trash text-xs"></i>
                    </button>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', html);
        
        // Re-initialize select2 for new element
        $('.select2-obat').select2({
            width: '100%',
            placeholder: "Ketik/Pilih Obat..."
        });
        
        medicineCount++;
    });
</script>
@endsection