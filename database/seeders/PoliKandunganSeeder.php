<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Poli;
use App\Models\JadwalDokter;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PoliKandunganSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Poli Kandungan
        $createdPoli = Poli::firstOrCreate(
            ['name' => 'Poli Kandungan'],
            [
                'ruangan' => 'R.303', 
                'description' => 'Layanan kebidanan dan kandungan'
            ]
        );

        $doctorNames = ['dr. Sarah Ayu, Sp.OG', 'dr. Bima Sakti, Sp.OG'];

        foreach ($doctorNames as $index => $name) {
            $doctor = User::firstOrCreate(
                ['email' => str_replace([' ', ','], ['.', ''], strtolower($name)) . '@klinik.com'],
                [
                    'name' => $name,
                    'password' => Hash::make('password123'),
                    'role' => 'dokter',
                    'poli_id' => $createdPoli->id,
                    'spesialisasi' => $createdPoli->name,
                    'price' => rand(150, 350) * 1000,
                    'phone' => '0812' . rand(10000000, 99999999),
                ]
            );

            // Create Schedules (Senin - Jumat)
            $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
            $isMorningShift = ($index % 2 == 0);

            foreach ($days as $day) {
                JadwalDokter::firstOrCreate([
                    'dokter_id' => $doctor->id,
                    'hari' => $day,
                ], [
                    'jam_mulai' => $isMorningShift ? '08:00:00' : '13:00:00',
                    'jam_selesai' => $isMorningShift ? '12:00:00' : '17:00:00',
                    'status' => 'aktif',
                ]);
            }
        }
    }
}
