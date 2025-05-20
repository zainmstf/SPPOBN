<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RekomendasiNutrisi extends Model
{
    use HasFactory;

    protected $table = 'rekomendasi_nutrisi';
    protected $fillable = [
        'solusi_id',
        'nutrisi',
        'kontraindikasi',
        'alternatif'
    ];

    public function solusi()
    {
        return $this->belongsTo(Solusi::class);
    }

    public function sumberNutrisi()
    {
        return $this->hasMany(SumberNutrisi::class, 'nutrisi', 'nutrisi');
    }
}
