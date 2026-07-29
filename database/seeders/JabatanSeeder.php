<?php

namespace Database\Seeders;

use App\Models\Jabatan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class JabatanSeeder extends Seeder
{
    public function run(): void
    {
        $jabatans = [
            'Kabid',
            'Kasubag',
            'KASI',
            'Kepala Bidang',
        ];

        foreach ($jabatans as $nama) {
            Jabatan::create([
                'nama_jabatan' => $nama,
                'slug'         => Str::slug($nama),
            ]);
        }
    }
}
