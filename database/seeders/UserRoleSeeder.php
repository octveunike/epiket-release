<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserRoleSeeder extends Seeder
{
    public function run(): void
    {
        $users = [];

        // Admin
        $users[] = [
            'id' => 1,
            'nama' => 'Administrator',
            'username' => 'admin',
            'email' => 'admin@epiket.test',
            'password' => Hash::make('admin'),
            'status' => 1,
        ];

        // Petugas
        $users[] = [
            'id' => 2,
            'nama' => 'Petugas Piket',
            'username' => 'petugas_piket',
            'email' => 'petugas@epiket.test',
            'password' => Hash::make('petugas_piket'),
            'status' => 1,
        ];

        // Wali kelas (10)
        for ($i = 1; $i <= 10; $i++) {
            $users[] = [
                'id' => 2 + $i,
                'nama' => "Wali Kelas $i",
                'username' => "guru$i",
                'email' => "guru$i@epiket.test",
                'password' => Hash::make('password'),
                'status' => 1,
            ];
        }

        // Ketua kelas (10)
        for ($i = 1; $i <= 10; $i++) {
            $users[] = [
                'id' => 12 + $i,
                'nama' => "Ketua Kelas $i",
                'username' => "ketua$i",
                'email' => "ketua$i@epiket.test",
                'password' => Hash::make('password'),
                'status' => 1,
            ];
        }

        DB::table('users')->insert($users);

        $userRoles = [];

        // Admin
        $userRoles[] = ['user_id' => 1, 'role_id' => 1, 'status' => 1];

        // Petugas
        $userRoles[] = ['user_id' => 2, 'role_id' => 2, 'status' => 1];

        // Wali kelas
        for ($i = 3; $i <= 12; $i++) {
            $userRoles[] = ['user_id' => $i, 'role_id' => 3, 'status' => 1];
        }

        // Ketua kelas
        for ($i = 13; $i <= 22; $i++) {
            $userRoles[] = ['user_id' => $i, 'role_id' => 7, 'status' => 1];
        }

        DB::table('user_role')->insert($userRoles);
    }
}