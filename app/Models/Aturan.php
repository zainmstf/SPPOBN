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

    public function logs()
    {
        return $this->hasMany(InferensiLog::class);
    }
    public function premisFakta()
    {
        $premisCodes = explode('^', $this->premis);
        return Fakta::whereIn('kode', $premisCodes);
    }

    public function konklusiFakta()
    {
        return $this->belongsTo(Fakta::class, 'konklusi', 'kode');
    }

    public function inferensiLogs()
    {
        return $this->hasMany(InferensiLog::class);
    }
}
