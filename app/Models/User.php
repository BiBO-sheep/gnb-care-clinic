<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; // Penting untuk API Flutter
use Illuminate\Database\Eloquent\SoftDeletes; // Untuk fitur hapus sementara
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    /** @use HasFactory\Illuminate\Database\Eloquent\Factories\HasFactory */
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'address',
        'blood_type',
        'emergency_contact_name',
        'emergency_contact_phone',
        'avatar',
        'poli_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // ==========================================
    // RELASI (RELATIONSHIPS)
    // ==========================================

    /**
     * Jika user adalah dokter, dia terikat pada satu poli
     */
    public function poli(): BelongsTo
    {
        return $this->belongsTo(Poli::class);
    }

    /**
     * Dokter memiliki banyak jadwal
     */
    public function jadwal(): HasMany
    {
        return $this->hasMany(JadwalDokter::class, 'dokter_id');
    }

    /**
     * Pasien memiliki banyak riwayat antrean
     */
    public function antrean_pasien(): HasMany
    {
        return $this->hasMany(Antrean::class, 'pasien_id');
    }

    /**
     * Dokter menangani banyak antrean
     */
    public function antrean_dokter(): HasMany
    {
        return $this->hasMany(Antrean::class, 'dokter_id');
    }

    /**
     * User memiliki banyak medical records
     */
    public function medical_records(): HasMany
    {
        return $this->hasMany(MedicalRecord::class);
    }

    /**
     * User memiliki banyak appointments
     */
    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }
}
