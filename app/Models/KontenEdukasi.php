<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KontenEdukasi extends Model
{
    use HasFactory;

    protected $table = 'konten_edukasi';
    protected $fillable = [
        'judul',
        'slug',
        'jenis',
        'kategori',
        'deskripsi',
        'konten',
        'path',
        'thumbnail',
        'status',
        'created_by',
        'view_count'
    ];

    protected $casts = [
        'status' => 'boolean'
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
