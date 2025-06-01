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
        'is_first' => 'boolean',
        'is_askable' => 'boolean',
        'is_default' => 'boolean'
    ];

    public function detailKonsultasi()
    {
        return $this->hasMany(DetailKonsultasi::class);
    }

    public function scopeAskable($query)
    {
        return $query->where('is_askable', true);
    }

    public function scopeByKategori($query, $kategori)
    {
        return $query->where('kategori', $kategori);
    }

    public function scopeFirst($query)
    {
        return $query->where('is_first', true);
    }
}
