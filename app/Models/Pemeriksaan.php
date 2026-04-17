<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pemeriksaan extends Model
{
    use HasFactory;

    protected $fillable = ['antrean_id', 'dokter_id', 'keluhan', 'diagnosa', 'heart_rate', 'temperature', 'next_assessment_date', 'biaya_pemeriksaan'];

    protected $casts = [
        'next_assessment_date' => 'datetime',
    ];

    public function antrean()
    {
        return $this->belongsTo(Antrean::class);
    }
    public function dokter()
    {
        return $this->belongsTo(User::class, 'dokter_id');
    }
    public function reseps()
    {
        return $this->hasMany(Resep::class);
    }
}
