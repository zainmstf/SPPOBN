<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InferensiLog extends Model
{
    use HasFactory;

    protected $table = 'inferensi_log';
    public $timestamps = false;

    protected $fillable = [
        'konsultasi_id',
        'aturan_id',
        'fakta_terbentuk',
        'premis_terpenuhi',
        'waktu'
    ];

    protected $casts = [
        'waktu' => 'datetime'
    ];

    public function konsultasi()
    {
        return $this->belongsTo(Konsultasi::class);
    }

    public function aturan()
    {
        return $this->belongsTo(Aturan::class);
    }
}
