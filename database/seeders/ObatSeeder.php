<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Obat;

class ObatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $obats = [
            ['nama_obat' => 'Paracetamol 500mg', 'kategori' => 'Analgesic', 'harga' => 5000, 'stok' => 100],
            ['nama_obat' => 'Amoxicillin 500mg', 'kategori' => 'Antibiotic', 'harga' => 15000, 'stok' => 50],
            ['nama_obat' => 'Ibuprofen 400mg', 'kategori' => 'Analgesic', 'harga' => 10000, 'stok' => 75],
            ['nama_obat' => 'Vitamin C 500mg', 'kategori' => 'Vitamin', 'harga' => 20000, 'stok' => 200],
            ['nama_obat' => 'Mefenamic Acid 500mg', 'kategori' => 'Analgesic', 'harga' => 12000, 'stok' => 60],
            ['nama_obat' => 'Cefadroxil 500mg', 'kategori' => 'Antibiotic', 'harga' => 25000, 'stok' => 40],
            ['nama_obat' => 'Antasida Doen Tablet', 'kategori' => 'Antacid', 'harga' => 8000, 'stok' => 120],
            ['nama_obat' => 'Omeprazole 20mg', 'kategori' => 'Antacid', 'harga' => 18000, 'stok' => 80],
            ['nama_obat' => 'Cetirizine 10mg', 'kategori' => 'Antihistamine', 'harga' => 15000, 'stok' => 90],
            ['nama_obat' => 'Loratadine 10mg', 'kategori' => 'Antihistamine', 'harga' => 14000, 'stok' => 85],
            ['nama_obat' => 'Dexamethasone 0.5mg', 'kategori' => 'Corticosteroid', 'harga' => 5000, 'stok' => 150],
            ['nama_obat' => 'Salbutamol 2mg', 'kategori' => 'Bronchodilator', 'harga' => 10000, 'stok' => 70],
            ['nama_obat' => 'OBH Combi Syrup 100ml', 'kategori' => 'Syrup', 'harga' => 25000, 'stok' => 45],
            ['nama_obat' => 'Sanmol Syrup 60ml', 'kategori' => 'Syrup', 'harga' => 22000, 'stok' => 50],
            ['nama_obat' => 'Betadine Antiseptic 15ml', 'kategori' => 'Antiseptic', 'harga' => 12000, 'stok' => 100],
        ];

        foreach ($obats as $obat) {
            Obat::updateOrCreate(
                ['nama_obat' => $obat['nama_obat']],
                $obat
            );
        }
    }
}
