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

        // Petugas Piket
        $users[] = [
            'id' => 2,
            'nama' => 'Petugas Piket',
            'username' => 'petugas_piket',
            'email' => 'petugas@epiket.test',
            'password' => Hash::make('petugas_piket'),
            'status' => 1,
        ];

        // Wali Kelas (X 1)
        $users[] = [
            'id' => 3,
            'nama' => 'Dra. Dian T Amperawati',
            'username' => 'wali_x1',
            'email' => 'dian@epiket.test',
            'password' => Hash::make('password'),
            'status' => 1,
        ];

        // Ketua Kelas (X 1)
        $users[] = [
            'id' => 4,
            'nama' => 'ABDUL GHANI AL-KHAIRI',
            'username' => 'ketua_x1',
            'email' => 'ketua@epiket.test',
            'password' => Hash::make('password'),
            'status' => 1,
        ];

        DB::table('users')->insert($users);

        $userRoles = [];

        // Admin
        $userRoles[] = ['user_id' => 1, 'role_id' => 1, 'status' => 1];

        // Petugas
        $userRoles[] = ['user_id' => 2, 'role_id' => 2, 'status' => 1];

        // Wali Kelas
        $userRoles[] = ['user_id' => 3, 'role_id' => 3, 'status' => 1];

        // Ketua Kelas
        $userRoles[] = ['user_id' => 4, 'role_id' => 4, 'status' => 1];

        DB::table('user_role')->insert($userRoles);
    }
}