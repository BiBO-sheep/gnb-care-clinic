<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transaksis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('antrean_id')->constrained('antreans')->cascadeOnDelete();
            $table->decimal('biaya_pemeriksaan', 12, 2)->default(0);
            $table->decimal('biaya_lab', 12, 2)->default(0);
            $table->decimal('biaya_obat', 12, 2)->default(0);
            $table->decimal('total_biaya', 12, 2)->default(0);
            $table->enum('status_pembayaran', ['pending', 'lunas'])->default('pending');
            $table->enum('metode_pembayaran', ['QRIS', 'Bank Transfer', 'Tunai'])->nullable();
            $table->string('bukti_transfer')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksis');
    }
};
