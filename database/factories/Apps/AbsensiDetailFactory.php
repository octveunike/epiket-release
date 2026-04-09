<?php

namespace Database\Factories\Apps;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Admin\Siswa;
use App\Models\Apps\Absensi;

class AbsensiDetailFactory extends Factory
{
    public function definition(): array
    {
        return [
            'absensi_id' => Absensi::inRandomOrder()->first()->id,
            'siswa_id' => Siswa::inRandomOrder()->first()->id,
            'status_absensi_id' => rand(1, 4),
            'is_full_day' => fake()->boolean(),
            'status' => 1,
        ];
    }
}