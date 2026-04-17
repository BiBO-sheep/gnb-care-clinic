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
        Schema::create('antreans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pasien_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('poli_id')->constrained('polis')->cascadeOnDelete();
            $table->foreignId('dokter_id')->constrained('users')->cascadeOnDelete();
            $table->date('tanggal');
            $table->time('waktu_booking');
            $table->string('nomor_antrean');
            $table->enum('status', ['pending', 'scheduled', 'check_in', 'pemeriksaan', 'selesai', 'batal'])->default('pending');
            $table->integer('estimasi_waktu_tunggu')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('antreans');
    }
};
