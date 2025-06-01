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
        'hasil_konsultasi',
        'status',
        'sesi',
        'completed_at'
    ];

    protected $casts = [
        'completed_at' => 'datetime',
        'hasil_konsultasi' => 'array'
    ];

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

    public function getJawabanFakta($kodeFakta)
    {
        $detail = $this->detailKonsultasi()
            ->whereHas('fakta', function ($q) use ($kodeFakta) {
                $q->where('kode', $kodeFakta);
            })
            ->first();

        return $detail ? $detail->jawaban : null;
    }

    public function getFaktaTerjawab()
    {
        return $this->detailKonsultasi()
            ->with('fakta')
            ->get()
            ->pluck('fakta.kode')
            ->toArray();
    }

    public function getFaktaAntara()
    {
        return $this->inferensiLog()
            ->where('fakta_terbentuk', 'like', 'FA%')
            ->pluck('fakta_terbentuk')
            ->toArray();
    }

    public function getSolusi()
    {
        return $this->inferensiLog()
            ->where('fakta_terbentuk', 'like', 'S%')
            ->pluck('fakta_terbentuk')
            ->toArray();
    }

    public function currentPertanyaan()
    {
        return $this->belongsTo(Fakta::class, 'pertanyaan_terakhir', 'kode');
    }

    public function feedback()
    {
        return $this->hasOne(Feedback::class);
    }
}
