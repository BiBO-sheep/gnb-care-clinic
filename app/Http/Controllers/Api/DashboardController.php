<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function getDashboardData(Request $request)
    {
        $userId = Auth::id();

        // 1. Fetch Upcoming Appointment (Yang terdekat)
        $upcoming = Appointment::with(['poli', 'dokter'])
            ->where('user_id', $userId)
            ->whereIn('status', ['scheduled', 'check_in'])
            ->orderBy('tanggal', 'asc')
            ->orderBy('jam', 'asc')
            ->first();

        // 2. Health Tips (Static data for now, could be from DB)
        $healthTips = [
            [
                'id' => 1,
                'title' => 'Pentingnya Hidrasi',
                'description' => 'Minum air mineral minimal 2 liter sehari biar fokus belajar makin tajam.',
                'category' => 'Nutrisi',
                'icon' => 'water_drop',
                'image' => 'https://images.unsplash.com/photo-1548919973-5cdf5916ad52?q=80&w=400&auto=format&fit=crop'
            ],
            [
                'id' => 2,
                'title' => 'Power Nap',
                'description' => 'Tidur siang 20 menit aja udah cukup buat reset energi lanjut nugas malam.',
                'category' => 'Gaya Hidup',
                'icon' => 'bedtime',
                'image' => 'https://images.unsplash.com/photo-1511295744334-f4d56f114d40?q=80&w=400&auto=format&fit=crop'
            ],
            [
                'id' => 3,
                'title' => 'Pola Makan 3J',
                'description' => 'Atur Jumlah, Jenis, dan Jadwal makan biar lambung tetap aman pas deadline.',
                'category' => 'Nutrisi',
                'icon' => 'restaurant',
                'image' => 'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?q=80&w=400&auto=format&fit=crop'
            ],
            [
                'id' => 4,
                'title' => 'Stretching Rutin',
                'description' => 'Lakukan peregangan 5 menit tiap jam biar otot gak kaku di depan laptop.',
                'category' => 'Gaya Hidup',
                'icon' => 'directions_run',
                'image' => 'https://images.unsplash.com/photo-1517836357463-d25dfeac3438?q=80&w=400&auto=format&fit=crop'
            ],
            [
                'id' => 5,
                'title' => 'Manajemen Stress',
                'description' => 'Luangkan waktu 15 menit buat meditasi atau dengerin musik tenang tiap hari.',
                'category' => 'Mental',
                'icon' => 'spa',
                'image' => 'https://images.unsplash.com/photo-1506126613408-eca07ce68773?q=80&w=400&auto=format&fit=crop'
            ],
        ];

        return response()->json([
            'success' => true,
            'upcoming_appointment' => $upcoming,
            'health_tips' => $healthTips
        ]);
    }
}
