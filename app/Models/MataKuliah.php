<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MataKuliah extends Model
{
    protected $table = 'mata_kuliahs';

    protected $fillable = [
        'nama_matkul',
        'dosen_id',
    ];

    // Relasi ke dosen
    public function dosen()
    {
        return $this->belongsTo(User::class, 'dosen_id');
    }

    // Relasi ke kehadiran
    public function kehadirans()
    {
        return $this->hasMany(Kehadiran::class);
    }

    // Relasi ke sesi presensi
    public function sesiPresensis()
    {
        return $this->hasMany(SesiPresensi::class);
    }
}