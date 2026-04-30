<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolesSeeder::class,
            UserRoleSeeder::class,
            StatusAbsensiSeeder::class,
            StatusSiswaSeeder::class,
            StatusValidasiSeeder::class,
            JamAbsensiSeeder::class,
            PeriodeAkademikSeeder::class,
            KelasSeeder::class,
        ]);

        User::create([
            'nama'     => 'Testing User',
            'username' => 'testing',
            'email'    => 'testing@epiket.com',
            'password' => Hash::make('testing'),
            'status'   => 1,
        ]);
    }
}