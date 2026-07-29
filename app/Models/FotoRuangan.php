<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FotoRuangan extends Model
{
    protected $fillable = ['ruangan_id', 'nama_foto'];

    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class);
    }
}
