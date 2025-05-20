<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fakta extends Model
{
    use HasFactory;

    protected $table = 'fakta';
    protected $fillable = [
        'kode',
        'deskripsi',
        'kategori',
        'pertanyaan',
        'is_first',
        'is_askable',
        'is_default'
    ];

    protected $casts = [
        'is_first' => 'boolean'
    ];

    public function jawaban()
    {
        return $this->hasMany(DetailKonsultasi::class);
    }

    public function nextFaktaYa()
    {
        return $this->belongsTo(Fakta::class, 'next_if_yes', 'kode');
    }

    public function nextFaktaTidak()
    {
        return $this->belongsTo(Fakta::class, 'next_if_no', 'kode');
    }
}
