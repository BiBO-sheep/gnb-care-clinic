<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resep extends Model
{
    use HasFactory;

    protected $fillable = ['pemeriksaan_id', 'obat_id', 'jumlah', 'dosis', 'instruksi', 'total_harga', 'status'];

    public function pemeriksaan()
    {
        return $this->belongsTo(Pemeriksaan::class);
    }
    public function obat()
    {
        return $this->belongsTo(Obat::class);
    }
}
