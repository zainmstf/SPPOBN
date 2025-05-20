<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailKonsultasi extends Model
{
    use HasFactory;

    protected $table = 'detail_konsultasi';
    protected $fillable = [
        'konsultasi_id',
        'fakta_id',
        'jawaban'
    ];

    public function konsultasi()
    {
        return $this->belongsTo(Konsultasi::class);
    }

    public function fakta()
    {
        return $this->belongsTo(Fakta::class);
    }
}