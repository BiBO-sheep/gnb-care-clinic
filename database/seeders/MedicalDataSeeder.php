<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Poli;
use App\Models\Doctor;
use App\Models\User;
use App\Models\Appointment;
use App\Models\MedicalRecord;
use App\Models\Prescription;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class MedicalDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Dapatkan atau buat User pasien
        $user = User::first();
        if (!$user) {
            $user = User::create([
                'name' => 'Pasien Dummy',
                'email' => 'pasien@example.com',
                'password' => Hash::make('password'),
                // Silakan sesuaikan apabila ada field lain yang mandatory
            ]);
        }

        // 2. Persiapkan Poli & Dokter (Jika belum ada, kita buatkan)
        
        // Poli Dalam (Baru)
        $poliDalam = Poli::firstOrCreate(['name' => 'Poli Penyakit Dalam']);
        $doctorDalam = Doctor::firstOrCreate(
            ['spesialisasi' => 'Spesialis Penyakit Dalam'],
            [
                'poli_id' => $poliDalam->id,
                'nama_dokter' => 'dr. Herman Susanto, Sp.PD',
                'foto_url' => 'https://randomuser.me/api/portraits/men/75.jpg',
            ]
        );

        // Poli Gigi
        $poliGigi = Poli::firstOrCreate(['name' => 'Poli Gigi']);
        $doctorGigi = Doctor::firstOrCreate(
            ['spesialisasi' => 'Dokter Gigi'],
            [
                'poli_id' => $poliGigi->id,
                'nama_dokter' => 'drg. Dian Kusuma',
                'foto_url' => 'https://randomuser.me/api/portraits/women/89.jpg',
            ]
        );

        // Poli Umum
        $poliUmum = Poli::firstOrCreate(['name' => 'Poli Umum']);
        $doctorUmum = Doctor::firstOrCreate(
            ['spesialisasi' => 'Dokter Umum'],
            [
                'poli_id' => $poliUmum->id,
                'nama_dokter' => 'dr. Andi Pratama',
                'foto_url' => 'https://randomuser.me/api/portraits/men/32.jpg',
            ]
        );

        // Poli Anak
        $poliAnak = Poli::firstOrCreate(['name' => 'Poli Anak']);
        $doctorAnak = Doctor::firstOrCreate(
            ['spesialisasi' => 'Spesialis Anak'],
            [
                'poli_id' => $poliAnak->id,
                'nama_dokter' => 'dr. Budi Santoso, Sp.A',
                'foto_url' => 'https://randomuser.me/api/portraits/men/67.jpg',
            ]
        );

        // 3. Looping Data Skenario Medis (5 Skenario)
        $scenarios = [
            [
                'doctor' => $doctorGigi,
                'diagnosis' => 'Pulpitis Akut (Sakit Gigi Melubang)',
                'treatment' => 'Pembersihan kavitas dan tumpatan sementara',
                'notes' => 'Pasien mengeluh sakit gigi berdenyut kuat terutama di malam hari. Terdapat karies profunda pada gigi 46.',
                'medicines' => [
                    ['name' => 'Cataflam', 'dosage' => '50mg', 'rules' => '2x1 sesudah makan'],
                    ['name' => 'Amoxicillin', 'dosage' => '500mg', 'rules' => '3x1 habiskan'],
                ]
            ],
            [
                'doctor' => $doctorUmum,
                'diagnosis' => 'Common Cold / Influenza',
                'treatment' => 'Istirahat cukup dan hidrasi optimal',
                'notes' => 'Demam ringan (37.8 C), hidung tersumbat, batuk berdahak sejak 2 hari yang lalu.',
                'medicines' => [
                    ['name' => 'Paracetamol', 'dosage' => '500mg', 'rules' => '3x1 jika demam'],
                    ['name' => 'Vitamin C', 'dosage' => '500mg', 'rules' => '1x1 sesudah makan'],
                ]
            ],
            [
                'doctor' => $doctorDalam,
                'diagnosis' => 'Gastritis (Asam Lambung)',
                'treatment' => 'Diet rendah asam dan pola makan teratur',
                'notes' => 'Pasien mengeluh nyeri ulu hati, mual, sering telat makan karena kesibukan kerja.',
                'medicines' => [
                    ['name' => 'Omeprazole', 'dosage' => '20mg', 'rules' => '2x1 sebelum makan'],
                    ['name' => 'Antasida Doen', 'dosage' => 'Tablet', 'rules' => '3x1 kunyah'],
                ]
            ],
            [
                'doctor' => $doctorAnak,
                'diagnosis' => 'Acute Nasopharyngitis',
                'treatment' => 'Observasi suhu tubuh dan kompres hangat',
                'notes' => 'Anak rewel, hidung tersumbat, nafsu makan turun, suhu 38.2 C.',
                'medicines' => [
                    ['name' => 'Sanmol Syrup', 'dosage' => '120mg/5ml', 'rules' => '3x1.5 sdt'],
                    ['name' => 'Mucos Drop', 'dosage' => '15mg/ml', 'rules' => '3x0.5 ml'],
                ]
            ],
            [
                'doctor' => $doctorUmum,
                'diagnosis' => 'Tension-type Headache',
                'treatment' => 'Manajemen stres, perbanyak istirahat',
                'notes' => 'Sakit kepala terasa seperti diikat, tegang pada area leher belakang. Kurang tidur dalam seminggu terakhir.',
                'medicines' => [
                    ['name' => 'Ibuprofen', 'dosage' => '400mg', 'rules' => '2x1 sesudah makan'],
                    ['name' => 'Neurobion Forte', 'dosage' => '1 Tab', 'rules' => '1x1 sesudah makan'],
                ]
            ]
        ];

        // 4. Eksekusi Input Data (Appointments -> Medical Records -> Prescriptions)
        foreach ($scenarios as $index => $scenario) {
            $doc = $scenario['doctor'];
            
            // Tanggal mundur agar rekam medis terlihat memiliki history yang berbeda
            $tanggal = Carbon::now()->subDays(14 - ($index * 2))->format('Y-m-d');
            
            // Insert Appointment (Status Selesai)
            $appointment = Appointment::create([
                'user_id' => $user->id,
                'poli_id' => $doc->poli_id,
                'queue_number' => 'Q-' . str_pad(rand(1, 100), 3, '0', STR_PAD_LEFT),
                'tanggal' => $tanggal,
                'jam' => '09:00:00',
                'status' => 'Selesai',
            ]);

            // Insert Medical Record menggunakan ID appointment yang baru dibuat
            $medicalRecord = MedicalRecord::create([
                'user_id' => $user->id,
                'doctor_id' => $doc->id,
                'appointment_id' => $appointment->id,
                'diagnosis' => $scenario['diagnosis'],
                'doctor_notes' => $scenario['notes'],
                'treatment_plan' => $scenario['treatment'],
                'created_at' => $tanggal . ' 09:30:00',
                'updated_at' => $tanggal . ' 09:30:00',
            ]);

            // Insert Obat-obatan ke Prescription menggunakan medical_record_id yang baru dibuat
            foreach ($scenario['medicines'] as $med) {
                Prescription::create([
                    'medical_record_id' => $medicalRecord->id,
                    'medicine_name' => $med['name'],
                    'dosage' => $med['dosage'],
                    'rules' => $med['rules'],
                ]);
            }
        }
    }
}
