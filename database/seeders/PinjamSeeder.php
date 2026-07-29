<?php

namespace Database\Seeders;

use App\Models\Pinjam;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PinjamSeeder extends Seeder
{
    public function run(): void
    {
        $pinjams = [
            'Rapat Evaluasi Kinerja',
            'Rapat Bulanan',
            'Rapat Tahunan',
        ];

        foreach ($pinjams as $tujuan) {
            Pinjam::create([
                'tujuan_pinjam' => $tujuan,
                'slug'          => Str::slug($tujuan),
            ]);
        }
    }
}
