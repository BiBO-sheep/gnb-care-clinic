<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    use HasFactory;

    protected $fillable = [
        'poli_id',
        'nama_dokter',
        'spesialisasi',
        'price',
        'foto_url',
    ];

    public function poli()
    {
        return $this->belongsTo(Poli::class);
    }

    public function medicalRecords()
    {
        return $this->hasMany(MedicalRecord::class);
    }
}
