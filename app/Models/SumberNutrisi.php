<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SumberNutrisi extends Model
{
    use HasFactory;

    protected $table = 'sumber_nutrisi';
    protected $fillable = [
        'nutrisi',
        'jenis_sumber',
        'nama_sumber',
        'takaran',
        'catatan',
        'image'
    ];

    public function rekomendasiNutrisi()
    {
        return $this->belongsTo(RekomendasiNutrisi::class, 'nutrisi', 'nutrisi');
    }
}
