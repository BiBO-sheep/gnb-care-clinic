<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Appointment;
use App\Models\MedicalRecord;
use App\Models\Prescription;
use App\Models\Invoice;
use App\Models\Obat;
use Illuminate\Support\Facades\DB;

#[Layout('layouts.admin')]
class DoctorExamine extends Component
{
    public $appointmentId;
    public $keluhan;
    public $diagnosis;
    public $tindakan;
    
    public $medicines = []; // Array of ['obat_id' => '', 'qty' => 1, 'rules' => '']

    public function mount($id)
    {
        $this->appointmentId = $id;
        $appointment = Appointment::with(['user', 'poli', 'dokter'])->findOrFail($id);
        $user = auth()->user();

        if ($user->role === 'dokter' && $user->poli_id !== $appointment->poli_id) {
            abort(403, 'Akses Ditolak: Pasien ini terdaftar di poli lain.');
        }
        
        if ($user->role !== 'admin' && $appointment->status !== 'pemeriksaan') {
            $appointment->update(['status' => 'pemeriksaan']);
        }

        $this->addMedicine(); // Default 1 empty row
    }

    public function addMedicine()
    {
        $this->medicines[] = ['obat_id' => '', 'qty' => 1, 'rules' => ''];
    }

    public function removeMedicine($index)
    {
        unset($this->medicines[$index]);
        $this->medicines = array_values($this->medicines); // Reindex
    }

    public function simpanResep()
    {
        $appointment = Appointment::findOrFail($this->appointmentId);
        $user = auth()->user();

        if ($user->role === 'admin') {
            session()->flash('error', 'Akses Ditolak: Admin tidak diizinkan mengisi rekam medis.');
            return;
        }

        if ($user->role === 'dokter' && $user->poli_id !== $appointment->poli_id) {
            session()->flash('error', 'Akses Ditolak: Anda tidak dapat memeriksa pasien dari poli lain.');
            return;
        }

        $this->validate([
            'keluhan' => 'required|string',
            'diagnosis' => 'required|string',
            'tindakan' => 'required|string',
        ]);

        DB::beginTransaction();
        try {
            $consultationPrice = $appointment->dokter->price ?? 150000;

            $record = MedicalRecord::create([
                'user_id'        => $appointment->user_id,
                'doctor_id'      => $appointment->dokter_id ?? 1,
                'appointment_id' => $appointment->id,
                'keluhan'        => $this->keluhan,
                'diagnosis'      => $this->diagnosis,
                'tindakan'       => $this->tindakan,
                'doctor_notes'   => 'Selesai diperiksa dokter.',
                'treatment_plan' => $this->tindakan,
            ]);

            $totalMedicines = 0;
            foreach ($this->medicines as $med) {
                if (!empty($med['obat_id'])) {
                    $obat = Obat::find($med['obat_id']);
                    if ($obat) {
                        $qty = $med['qty'] ?? 1;
                        $price = $obat->harga * $qty;
                        $totalMedicines += $price;

                        Prescription::create([
                            'medical_record_id' => $record->id,
                            'obat_id'           => $obat->id,
                            'medicine_name'     => $obat->nama_obat,
                            'dosage'            => $qty . ' Pcs',
                            'rules'             => $med['rules'] ?? '-',
                            'price'             => $price,
                        ]);
                    }
                }
            }

            Invoice::create([
                'appointment_id'    => $appointment->id,
                'user_id'           => $appointment->user_id,
                'total_consultation'=> $consultationPrice,
                'total_medicines'   => $totalMedicines,
                'grand_total'       => $consultationPrice + $totalMedicines,
                'status'            => 'unpaid',
            ]);

            $appointment->update(['status' => 'selesai']);
            $appointment->user->notify(new \App\Notifications\ExaminationCompletedNotification());

            if ($appointment->user && $appointment->user->fcm_token) {
                try {
                    putenv('GOOGLE_APPLICATION_CREDENTIALS=' . storage_path('app/firebase-adminsdk.json'));
                    $client = new \Google_Client();
                    $client->useApplicationDefaultCredentials();
                    $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
                    $httpClient = $client->authorize();

                    $message = [
                        'message' => [
                            'token' => $appointment->user->fcm_token,
                            'notification' => [
                                'title' => 'Pemeriksaan Selesai',
                                'body' => 'Pemeriksaan selesai. Resep dan tagihan sudah terbit.'
                            ],
                            'android' => [
                                'priority' => 'high'
                            ]
                        ]
                    ];

                    $projectId = 'gandb-care-clinic';
                    $httpClient->post("https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send", [
                        'json' => $message
                    ]);
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('FCM Send Error: ' . $e->getMessage());
                }
            }

            DB::commit();

            session()->flash('success', 'Pemeriksaan selesai! Tagihan otomatis dihitung dan siap dibayar oleh Kasir.');
            return $this->redirect('/klinik/queue', navigate: true);

        } catch (\Exception $e) {
            DB::rollback();
            session()->flash('error', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $appointment = Appointment::with(['user', 'poli', 'dokter'])->findOrFail($this->appointmentId);
        $obats = Obat::where('stok', '>', 0)->get();

        return view('livewire.admin.doctor.examine', compact('appointment', 'obats'));
    }
}
