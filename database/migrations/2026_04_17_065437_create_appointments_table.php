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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // Pasien yang login
            $table->unsignedBigInteger('poli_id'); // Poli yang dipilih
            $table->string('tanggal'); // Tanggal berobat
            $table->string('jam'); // Jam berobat
            $table->string('status')->default('confirmed'); // Otomatis confirmed (Opsi A)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
