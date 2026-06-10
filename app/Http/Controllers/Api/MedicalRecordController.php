<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\MedicalRecord;
use Illuminate\Http\Request;

class MedicalRecordController extends Controller
{
    public function index(Request $request)
    {
        // Ambil rekam medis milik user yang login, beserta data dokter, resep, dan appointment (untuk poli & tanggal)
        $records = MedicalRecord::with(['doctor', 'prescriptions', 'appointment.poli'])
                    ->where('user_id', $request->user()->id)
                    ->orderBy('created_at', 'desc')
                    ->get();

        // Tambahkan data tanggal dan poli ke setiap record agar mudah dibaca Flutter
        foreach ($records as $record) {
            $record->tanggal = $record->appointment ? $record->appointment->tanggal : null;
            $record->poli_name = $record->appointment && $record->appointment->poli ? $record->appointment->poli->name : null;
        }

        return response()->json([
            'success' => true,
            'data' => $records
        ], 200);
    }
}