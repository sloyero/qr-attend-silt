<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SesiPresensi extends Model
{
    protected $table = 'sesi_presensis';

    protected $fillable = [
        'dosen_id',
        'mata_kuliah_id',
        'token',
        'durasi_menit',
        'started_at',
        'expired_at',
        'is_active',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'expired_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    // Relasi ke dosen
    public function dosen()
    {
        return $this->belongsTo(User::class, 'dosen_id');
    }

    // Relasi ke mata kuliah
    public function mataKuliah()
    {
        return $this->belongsTo(MataKuliah::class);
    }

    // Relasi ke kehadiran
    public function kehadirans()
    {
        return $this->hasMany(Kehadiran::class);
    }

    // Cek apakah sesi sudah expired
    public function isExpired()
    {
        return now()->greaterThan($this->expired_at);
    }

    // Cek apakah sesi masih aktif
    public function isActive()
    {
        return $this->is_active && !$this->isExpired();
    }
}
