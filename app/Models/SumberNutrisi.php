<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SumberNutrisi extends Model
{
    use HasFactory;

    protected $table = 'sumber_nutrisi';
    protected $fillable = [
        'rekomendasi_nutrisi_id',
        'jenis_sumber',
        'nama_sumber',
        'image',
        'takaran',
        'catatan'
    ];

    public function rekomendasiNutrisi()
    {
        return $this->belongsTo(RekomendasiNutrisi::class, 'rekomendasi_nutrisi_id');
    }
    public function scopeByNutrisi($query, $nutrisi)
    {
        return $query->where('nutrisi', $nutrisi);
    }

    public function scopeByJenisSumber($query, $jenis)
    {
        return $query->where('jenis_sumber', $jenis);
    }
}
