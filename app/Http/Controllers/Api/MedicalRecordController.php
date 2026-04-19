<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MedicalRecord;
use Illuminate\Http\Request;

class MedicalRecordController extends Controller
{
    public function getExamResults(Request $request)
    {
        $records = MedicalRecord::with(['appointment', 'appointment.poli', 'prescriptions', 'doctor'])
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $records
        ]);
    }
}
