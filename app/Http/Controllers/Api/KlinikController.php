<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Poli;
use App\Models\User;

class KlinikController extends Controller
{
    // Ambil semua daftar Poli
    public function getPoli()
    {
        $polis = Poli::all();

        return response()->json([
            'status' => 'success',
            'data' => $polis
        ]);
    }

    // Ambil daftar Dokter berdasarkan ID Poli
    public function getDokterByPoli($poli_id)
    {
        // Cari user yang rolenya 'dokter' dan poli_id nya sesuai
        $dokters = User::where('role', 'dokter')
            ->where('poli_id', $poli_id)
            ->with('jadwal') // Bawa juga data jadwalnya sekalian
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $dokters
        ]);
    }
}
