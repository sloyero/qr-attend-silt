<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kehadiran extends Model
{
    protected $fillable = [
        'user_id',
        'mata_kuliah_id',
        'sesi_presensi_id',
        'waktu_scan',
        'status',
    ];

    // Relasi ke user (mahasiswa)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke mata kuliah
    public function mataKuliah()
    {
        return $this->belongsTo(MataKuliah::class);
    }

    // Relasi ke sesi presensi
    public function sesiPresensi()
    {
        return $this->belongsTo(SesiPresensi::class);
    }
}