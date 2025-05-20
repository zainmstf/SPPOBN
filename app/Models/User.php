<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';
    protected $fillable = [
        'username',
        'password',
        'nama_lengkap',
        'email',
        'no_telepon',
        'alamat',
        'role'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function konsultasi()
    {
        return $this->hasMany(Konsultasi::class);
    }

    public function feedback()
    {
        return $this->hasMany(Feedback::class);
    }

    public function kontenEdukasi()
    {
        return $this->hasMany(KontenEdukasi::class, 'created_by');
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }
}
