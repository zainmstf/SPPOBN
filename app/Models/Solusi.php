<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Solusi extends Model
{
    use HasFactory;

    protected $table = 'solusi';
    protected $fillable = [
        'kode',
        'nama',
        'deskripsi',
        'peringatan_konsultasi',
        'is_default'
    ];

    public function rekomendasiNutrisi()
    {
        return $this->hasMany(RekomendasiNutrisi::class);
    }

    public function aturan()
    {
        return $this->hasMany(Aturan::class);
    }

    public function konsultasi()
    {
        return $this->belongsToMany(Konsultasi::class, 'konsultasi_solusi');
    }

    public function inferensiLogs()
    {
        return $this->hasManyThrough(InferensiLog::class, Aturan::class);
    }
}
