<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\MedicalRecord;
use Illuminate\Http\Request;

class MedicalRecordController extends Controller
{
    public function index(Request $request)
    {
        // Ambil rekam medis milik user yang login, beserta data dokter dan resepnya
        $records = MedicalRecord::with(['doctor', 'prescriptions'])
                    ->where('user_id', $request->user()->id)
                    ->orderBy('created_at', 'desc')
                    ->get();

        return response()->json([
            'success' => true,
            'data' => $records
        ], 200);
    }
}