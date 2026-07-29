<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ruangan extends Model
{
use HasFactory;

protected $fillable = [
    'kode_ruangan', 'nama_ruangan', 'slug', 'jumlah', 'keterangan', 'foto_ruangan',
];

public function fotos()
{
    return $this->hasMany(FotoRuangan::class, 'ruangan_id');
}
}
