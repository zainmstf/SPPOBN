<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aturan extends Model
{
    use HasFactory;

    protected $table = 'aturan';
    protected $fillable = [
        'kode',
        'deskripsi',
        'premis',
        'konklusi',
        'jenis_konklusi',
        'solusi_id',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function solusi()
    {
        return $this->belongsTo(Solusi::class);
    }
    public function inferensiLog()
    {
        return $this->hasMany(InferensiLog::class);
    }

    public function getPremisArray()
    {
        if (empty($this->premis)) {
            return [];
        }
        return explode('^', $this->premis);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByJenisKonklusi($query, $jenis)
    {
        return $query->where('jenis_konklusi', $jenis);
    }

    public function checkPremiseTerpenuhi($faktaTersedia)
    {
        $premisArray = $this->getPremisArray();

        if (empty($premisArray)) {
            return false;
        }

        foreach ($premisArray as $premis) {
            if (!in_array(trim($premis), $faktaTersedia)) {
                return false;
            }
        }

        return true;
    }
}
