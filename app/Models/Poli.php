<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Poli extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'description', 'ruangan'];

    public function dokters()
    {
        return $this->hasMany(User::class, 'poli_id');
    }
    public function antreans()
    {
        return $this->hasMany(Antrean::class);
    }
}
