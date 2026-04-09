<?php

namespace Database\Factories\Apps;

use Illuminate\Database\Eloquent\Factories\Factory;

class KelasFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nama_kelas' => fake()->randomElement([
                'X IPA 1', 'X IPA 2', 'X IPS 1',
                'XI IPA 1', 'XI IPA 2', 'XI IPS 1',
                'XII IPA 1', 'XII IPA 2', 'XII IPS 1', 'XII IPS 2',
            ]),
            'wali_kelas_id'      => null,
            'ketua_kelas_id'     => null,
            'periode_akademik_id' => 1,
            'status'             => 1,
        ];
    }
}