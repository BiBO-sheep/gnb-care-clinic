<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Menambahkan constraint relasi ke tabel polis
            $table->foreign('poli_id')->references('id')->on('polis')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Menghapus relasi jika di-rollback
            $table->dropForeign(['poli_id']);
        });
    }
};
