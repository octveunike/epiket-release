<?php

namespace Database\Factories\Apps;

use Illuminate\Database\Eloquent\Factories\Factory;

class DaftarTamuFactory extends Factory
{
    public function definition(): array
    {
        return [
            'tanggal_kunjungan' => fake()->date(),
            'nama' => fake()->name(),
            'lembaga_organisasi' => fake()->company(),
            'alamat' => fake()->address(),
            'orang_yang_dituju' => fake()->name(),
            'tujuan_kunjungan' => fake()->sentence(),
        ];
    }
}