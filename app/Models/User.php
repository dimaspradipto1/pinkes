<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
        'nip',
        'jabatan_nama',
        'pangkat_golongan',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    /** Cek apakah user adalah admin atau staf */
    public function isAdmin(): bool
    {
        return in_array($this->role, ['admin', 'staf']);
    }

    /** Cek apakah user adalah role user biasa (kabid, kasi, kasubag, kepalabidang) */
    public function isUser(): bool
    {
        return in_array($this->role, ['kabid', 'kasi', 'kasubag', 'kepalabidang']);
    }
}
