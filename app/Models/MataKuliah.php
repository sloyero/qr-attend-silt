<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MataKuliah extends Model
{
    protected $table = 'mata_kuliahs';

    protected $fillable = [
        'nama_matkul'
    ];

    public function kehadirans()
    {
        return $this->hasMany(Kehadiran::class);
    }
}