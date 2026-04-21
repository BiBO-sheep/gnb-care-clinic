<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Poli;
use App\Models\JadwalDokter;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Truncate Data (Clean Start)
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('users')->truncate();
        DB::table('polis')->truncate();
        DB::table('jadwal_dokters')->truncate();
        DB::table('appointments')->truncate(); // Ikut dibersihkan karena dependensi
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // 2. Master Data Poli
        $polis = [
            ['name' => 'Poli Umum', 'ruangan' => 'R.101', 'description' => 'Layanan kesehatan umum'],
            ['name' => 'Poli Gigi', 'ruangan' => 'R.102', 'description' => 'Layanan kesehatan gigi dan mulut'],
            ['name' => 'Poli Jantung', 'ruangan' => 'R.201', 'description' => 'Spesialis penyakit jantung dan pembuluh darah'],
            ['name' => 'Poli Anak', 'ruangan' => 'R.202', 'description' => 'Layanan kesehatan anak dan bayi'],
            ['name' => 'Poli Mata', 'ruangan' => 'R.301', 'description' => 'Spesialis kesehatan mata'],
            ['name' => 'Poli Kulit', 'ruangan' => 'R.302', 'description' => 'Spesialis kulit dan kelamin'],
        ];

        foreach ($polis as $p) {
            $createdPoli = Poli::create($p);

            // 3. Data Dokter (2 Dokter per Poli)
            $doctorNames = [
                'Poli Umum' => ['dr. Andi Setiawan', 'dr. Siti Aminah'],
                'Poli Gigi' => ['drg. Budi Santoso', 'drg. Maya Putri'],
                'Poli Jantung' => ['dr. Rio Pratama, Sp.JP', 'dr. Larasati, Sp.JP'],
                'Poli Anak' => ['dr. Kevin Wijaya, Sp.A', 'dr. Nina Marlina, Sp.A'],
                'Poli Mata' => ['dr. Hendra Kusuma, Sp.M', 'dr. Siska Amelia, Sp.M'],
                'Poli Kulit' => ['dr. Farid Aziz, Sp.KK', 'dr. Elena Rose, Sp.KK'],
            ];

            foreach ($doctorNames[$createdPoli->name] as $index => $name) {
                $doctor = User::create([
                    'name' => $name,
                    'email' => str_replace(' ', '.', strtolower($name)) . '@klinik.com',
                    'password' => Hash::make('password123'),
                    'role' => 'dokter',
                    'poli_id' => $createdPoli->id,
                    'spesialisasi' => $createdPoli->name,
                    'price' => rand(150, 350) * 1000,
                    'phone' => '0812' . rand(10000000, 99999999),
                ]);

                // 4. Jadwal Dokter (Senin - Jumat)
                $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
                $isMorningShift = ($index % 2 == 0); // Selang-seling shift pagi/sore

                foreach ($days as $day) {
                    JadwalDokter::create([
                        'dokter_id' => $doctor->id,
                        'hari' => $day,
                        'jam_mulai' => $isMorningShift ? '08:00:00' : '13:00:00',
                        'jam_selesai' => $isMorningShift ? '12:00:00' : '17:00:00',
                        'status' => 'aktif',
                    ]);
                }
            }
        }

        // 5. Admin Account
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@klinik.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
        ]);

        // 6. Contoh Akun Pasien untuk Testing
        User::create([
            'name' => 'Budi Pasien',
            'email' => 'budi@gmail.com',
            'password' => Hash::make('password123'),
            'role' => 'pasien',
            'phone' => '085712345678',
            'address' => 'Jl. Mawar No. 123, Jakarta',
        ]);
    }
}
