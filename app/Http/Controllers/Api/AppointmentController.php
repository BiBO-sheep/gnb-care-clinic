<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Tambahkan ini biar aman

class AppointmentController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'poli_id' => 'required',
            'tanggal' => 'required',
            'jam' => 'required',
        ]);

        // Pastikan user benar-benar login via sanctum
        if (!Auth::guard('sanctum')->check()) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $userId = Auth::guard('sanctum')->id();

        // Logika nomor antrean
        $countToday = Appointment::where('tanggal', $request->tanggal)->count();
        $queueNumber = 'A-' . ($countToday + 1);

        $appointment = Appointment::create([
            'user_id' => $userId,
            'poli_id' => $request->poli_id,
            'queue_number' => $queueNumber,
            'tanggal' => $request->tanggal,
            'jam' => $request->jam,
            'status' => 'confirmed'
        ]);

        // Load relasi user biar Flutter dapet nama pasiennya
        return response()->json([
            'success' => true,
            'message' => 'Booking sukses!',
            'data' => $appointment->load('user')
        ], 201);
    }
    // Tambahkan Request $request di dalam kurung ini
    public function getQueueStatus(Request $request)
    {
        $userId = $request->user()->id; // Sekarang $request tidak akan error lagi
        $today = date('M d, 2026');

        $myAppointment = Appointment::where('user_id', $userId)
            ->where('tanggal', $today)
            ->where('status', 'confirmed')
            ->first();

        $nowServing = Appointment::where('tanggal', $today)
            ->where('status', 'confirmed')
            ->min('queue_number');

        return response()->json([
            'my_queue' => $myAppointment ? $myAppointment->queue_number : 'A-0',
            'now_serving' => $nowServing ?? 'A-1',
        ]);
    }
    public function getHistory(Request $request)
    {
        $userId = $request->user()->id;

        // Ambil semua data appointment user ini, urutkan dari yang terbaru
        // Pastikan load relasi 'poli' dan 'user'
        $history = Appointment::with(['poli', 'user'])
            ->where('user_id', $userId)
            ->orderBy('tanggal', 'desc')
            ->orderBy('jam', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $history
        ], 200);
    }
}
