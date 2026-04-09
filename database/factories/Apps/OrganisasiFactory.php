<?php

namespace Database\Factories\Apps;

use Illuminate\Database\Eloquent\Factories\Factory;

class OrganisasiFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nama_organisasi' => fake()->randomElement([
                'OSIS', 'Pramuka', 'PMR', 'Basket', 'Futsal',
                'Paduan Suara', 'Rohis', 'KIR', 'English Club', 'Teater',
            ]),
            'pembina_id' => null,
            'status'     => 1,
        ];
    }
}