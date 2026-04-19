<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DoctorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('doctors')->insert([
            [
                'poli_id' => 1, // Poli Umum
                'nama_dokter' => 'dr. Andi Pratama',
                'spesialisasi' => 'Dokter Umum',
                'foto_url' => 'https://randomuser.me/api/portraits/men/32.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'poli_id' => 1, // Poli Umum
                'nama_dokter' => 'dr. Siti Aminah',
                'spesialisasi' => 'Dokter Umum',
                'foto_url' => 'https://randomuser.me/api/portraits/women/44.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'poli_id' => 2, // Poli Anak
                'nama_dokter' => 'dr. Budi Santoso, Sp.A',
                'spesialisasi' => 'Spesialis Anak',
                'foto_url' => 'https://randomuser.me/api/portraits/men/67.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'poli_id' => 2, // Poli Anak
                'nama_dokter' => 'dr. Rina Mulyani, Sp.A',
                'spesialisasi' => 'Spesialis Anak',
                'foto_url' => 'https://randomuser.me/api/portraits/women/68.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'poli_id' => 3, // Poli Gigi
                'nama_dokter' => 'drg. Dian Kusuma',
                'spesialisasi' => 'Dokter Gigi',
                'foto_url' => 'https://randomuser.me/api/portraits/women/89.jpg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
