<?php

namespace Database\Factories\Admin;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Admin\Guru;

class GuruFactory extends Factory
{
    protected $model = Guru::class;

    public function definition(): array
    {
        return [
            'nama_guru' => fake()->name(),
            'nip' => fake()->numerify('##################'),
            'user_id' => \App\Models\User::factory(),
            'status' => 1,
        ];
    }
}