<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Konsultasi extends Model
{
    use HasFactory;

    protected $table = 'konsultasi';
    protected $fillable = [
        'user_id',
        'pertanyaan_terakhir',
        'status',
        'sesi',
        'completed_at',
        'hasil_konsultasi'
    ];

    protected $dates = [
        'completed_at'
    ];

    public function currentPertanyaan()
    {
        return $this->belongsTo(Fakta::class, 'pertanyaan_terakhir', 'kode');
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function detailKonsultasi()
    {
        return $this->hasMany(DetailKonsultasi::class);
    }

    public function inferensiLog()
    {
        return $this->hasMany(InferensiLog::class);
    }

    public function feedback()
    {
        return $this->hasOne(Feedback::class);
    }

    public function logs()
    {
        return $this->hasMany(InferensiLog::class);
    }
}
