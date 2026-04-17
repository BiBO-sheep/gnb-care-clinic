<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalDokter extends Model
{
    use HasFactory;

    protected $fillable = ['dokter_id', 'hari', 'jam_mulai', 'jam_selesai', 'status'];

    public function dokter()
    {
        return $this->belongsTo(User::class, 'dokter_id');
    }
}
