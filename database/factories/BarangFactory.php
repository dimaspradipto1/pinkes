<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<\App\Models\Barang>
 */
class BarangFactory extends Factory
{
    public function definition(): array
    {
        $nama = fake()->unique()->words(2, true);

        return [
            'kode_barang' => 'BRG-' . fake()->unique()->numerify('###'),
            'nama_barang' => $nama,
            'slug'        => Str::slug($nama . '-' . Str::random(5)),
            'jumlah'      => 1,
            'keterangan'  => fake()->sentence(),
            'foto_barang' => '-',
        ];
    }
}
