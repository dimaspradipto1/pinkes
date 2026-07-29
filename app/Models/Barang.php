<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_barang', 'nama_barang', 'slug', 'jumlah', 'keterangan', 'foto_barang',
    ];

    public function fotos()
    {
        return $this->hasMany(FotoBarang::class, 'barang_id');
    }
}
