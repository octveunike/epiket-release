<?php

namespace Database\Factories\Apps;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Apps\Kelas;

class AbsensiFactory extends Factory
{
    public function definition(): array
    {
        return [
            'kelas_id' => Kelas::inRandomOrder()->first()->id,
            'tanggal' => fake()->dateTime(),
            'status_verifikasi_id' => rand(1, 5),
            'periode_akademik_id' => 1,
            'status' => 1,
        ];
    }
}