<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserRoleSeeder extends Seeder
{
    public function run(): void
    {
        // Data users
        $users = [
            [
                'id' => 1,
                'nama' => 'Administrator',
                'username' => 'admin',
                'email' => 'admin@epiket.test',
                'password' => Hash::make('admin'),
                'status' => 1,
                'user_input' => 'seeder',
                'tanggal_input' => now(),
            ],
            [
                'id' => 2,
                'nama' => 'Petugas Piket',
                'username' => 'petugas_piket',
                'email' => 'petugas@epiket.test',
                'password' => Hash::make('petugas_piket'),
                'status' => 1,
                'user_input' => 'seeder',
                'tanggal_input' => now(),
            ],
            [
                'id' => 3,
                'nama' => 'Wali Kelas',
                'username' => 'wali_kelas',
                'email' => 'walikelas@epiket.test',
                'password' => Hash::make('wali_kelas'),
                'status' => 1,
                'user_input' => 'seeder',
                'tanggal_input' => now(),
            ],
            [
                'id' => 4,
                'nama' => 'Pembina Ekskul',
                'username' => 'pembina_ekskul',
                'email' => 'pembina@epiket.test',
                'password' => Hash::make('pembina_ekskul'),
                'status' => 1,
                'user_input' => 'seeder',
                'tanggal_input' => now(),
            ],
            [
                'id' => 5,
                'nama' => 'Guru Mapel',
                'username' => 'guru',
                'email' => 'guru@epiket.test',
                'password' => Hash::make('guru'),
                'status' => 1,
                'user_input' => 'seeder',
                'tanggal_input' => now(),
            ],
            [
                'id' => 6,
                'nama' => 'Komdis OSIS',
                'username' => 'komdis',
                'email' => 'komdis@epiket.test',
                'password' => Hash::make('komdis'),
                'status' => 1,
                'user_input' => 'seeder',
                'tanggal_input' => now(),
            ],
            [
                'id' => 7,
                'nama' => 'Ketua Kelas',
                'username' => 'ketua_kelas',
                'email' => 'ketuakelas@epiket.test',
                'password' => Hash::make('ketua_kelas'),
                'status' => 1,
                'user_input' => 'seeder',
                'tanggal_input' => now(),
            ],
            [
                'id' => 8,
                'nama' => 'Siswa Satu',
                'username' => 'siswa1',
                'email' => 'siswa1@epiket.test',
                'password' => Hash::make('siswa1'),
                'status' => 1,
                'user_input' => 'seeder',
                'tanggal_input' => now(),
            ],
            [
                'id' => 9,
                'nama' => 'Siswa Dua',
                'username' => 'siswa2',
                'email' => 'siswa2@epiket.test',
                'password' => Hash::make('siswa2'),
                'status' => 1,
                'user_input' => 'seeder',
                'tanggal_input' => now(),
            ],
            [
                'id' => 10,
                'nama' => 'Siswa Tiga',
                'username' => 'siswa3',
                'email' => 'siswa3@epiket.test',
                'password' => Hash::make('siswa3'),
                'status' => 1,
                'user_input' => 'seeder',
                'tanggal_input' => now(),
            ],
            [
                'id' => 11,
                'nama' => 'Siswa Empat',
                'username' => 'siswa4',
                'email' => 'siswa4@epiket.test',
                'password' => Hash::make('siswa4'),
                'status' => 1,
                'user_input' => 'seeder',
                'tanggal_input' => now(),
            ],
            [
                'id' => 12,
                'nama' => 'Siswa Lima',
                'username' => 'siswa5',
                'email' => 'siswa5@epiket.test',
                'password' => Hash::make('siswa5'),
                'status' => 1,
                'user_input' => 'seeder',
                'tanggal_input' => now(),
            ],
        ];

        DB::table('users')->insert($users);

        // Mapping user ke role
        $userRoles = [
            ['user_id' => 1, 'role_id' => 1, 'status' => 1, 'user_input' => 'seeder', 'tanggal_input' => now()],
            ['user_id' => 2, 'role_id' => 2, 'status' => 1, 'user_input' => 'seeder', 'tanggal_input' => now()],
            ['user_id' => 3, 'role_id' => 3, 'status' => 1, 'user_input' => 'seeder', 'tanggal_input' => now()],
            ['user_id' => 4, 'role_id' => 4, 'status' => 1, 'user_input' => 'seeder', 'tanggal_input' => now()],
            ['user_id' => 5, 'role_id' => 5, 'status' => 1, 'user_input' => 'seeder', 'tanggal_input' => now()],
            ['user_id' => 6, 'role_id' => 6, 'status' => 1, 'user_input' => 'seeder', 'tanggal_input' => now()],
            ['user_id' => 7, 'role_id' => 7, 'status' => 1, 'user_input' => 'seeder', 'tanggal_input' => now()],
            ['user_id' => 8, 'role_id' => 8, 'status' => 1, 'user_input' => 'seeder', 'tanggal_input' => now()],
            ['user_id' => 9, 'role_id' => 8, 'status' => 1, 'user_input' => 'seeder', 'tanggal_input' => now()],
            ['user_id' => 10, 'role_id' => 8, 'status' => 1, 'user_input' => 'seeder', 'tanggal_input' => now()],
            ['user_id' => 11, 'role_id' => 8, 'status' => 1, 'user_input' => 'seeder', 'tanggal_input' => now()],
            ['user_id' => 12, 'role_id' => 8, 'status' => 1, 'user_input' => 'seeder', 'tanggal_input' => now()],
        ];

        DB::table('user_role')->insert($userRoles);
    }
}