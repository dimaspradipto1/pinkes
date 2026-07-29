<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<\App\Models\Pinjam>
 */
class PinjamFactory extends Factory
{
    public function definition(): array
    {
        $tujuan = fake()->unique()->sentence(3);

        return [
            'tujuan_pinjam' => $tujuan,
            'slug'          => Str::slug($tujuan . '-' . Str::random(5)),
        ];
    }
}
