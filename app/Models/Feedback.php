<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    use HasFactory;

    protected $table = 'feedback';
    protected $fillable = [
        'user_id',
        'konsultasi_id',
        'pesan',
        'rating'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function konsultasi()
    {
        return $this->belongsTo(Konsultasi::class);
    }
}