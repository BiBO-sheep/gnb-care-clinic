<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Antrean extends Model
{
    use HasFactory;

    protected $fillable = ['pasien_id', 'poli_id', 'dokter_id', 'tanggal', 'waktu_booking', 'nomor_antrean', 'status', 'estimasi_waktu_tunggu'];

    public function pasien()
    {
        return $this->belongsTo(User::class, 'pasien_id');
    }
    public function poli()
    {
        return $this->belongsTo(Poli::class);
    }
    public function dokter()
    {
        return $this->belongsTo(User::class, 'dokter_id');
    }
    public function pemeriksaan()
    {
        return $this->hasOne(Pemeriksaan::class);
    }
    public function transaksi()
    {
        return $this->hasOne(Transaksi::class);
    }
}
