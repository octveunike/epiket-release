<?php

namespace Database\Factories\Admin;

use Illuminate\Database\Eloquent\Factories\Factory;

class StaffFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nama_staff' => fake()->name(),
            'user_id' => \App\Models\User::factory(),
            'status' => 1,
        ];
    }
}