<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FotoBarang extends Model
{
    protected $fillable = ['barang_id', 'nama_foto'];

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }
}
