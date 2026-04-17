<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    use HasFactory;

    protected $fillable = ['antrean_id', 'biaya_pemeriksaan', 'biaya_lab', 'biaya_obat', 'total_biaya', 'status_pembayaran', 'metode_pembayaran', 'bukti_transfer'];

    public function antrean()
    {
        return $this->belongsTo(Antrean::class);
    }
}
