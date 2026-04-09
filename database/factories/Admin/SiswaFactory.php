<?php

namespace Database\Factories\Admin;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Admin\Siswa;

class SiswaFactory extends Factory
{
    protected $model = Siswa::class;

    public function definition(): array
    {
        return [
            'nis' => fake()->unique()->numerify('23#####'),
            'nama_siswa' => fake()->name(),
            'jenis_kelamin' => fake()->randomElement(['L', 'P']),
            'tanggal_masuk' => fake()->date(),
            'kelas_id' => 1,
            'status_siswa_id' => 1,
            'status' => 1,
        ];
    }
}