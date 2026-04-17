<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'poli_id',
        'queue_number',
        'tanggal',
        'jam',
        'status',
    ];

    // 👇 TAMBAHKAN INI SUPAYA BISA AMBIL DATA USER 👇
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function poli()
    {
        return $this->belongsTo(Poli::class);
    }
}
