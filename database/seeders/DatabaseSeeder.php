<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Admin\Guru;
use App\Models\Admin\Siswa;
use App\Models\Admin\Staff;
use App\Models\Apps\Kelas;
use App\Models\Apps\Organisasi;
use App\Models\Apps\Absensi;
use App\Models\Apps\AbsensiDetail;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolesSeeder::class,
            UserRoleSeeder::class,
            StatusAbsensiSeeder::class,
            StatusSiswaSeeder::class,
            StatusVerifikasiSeeder::class,
            JamAbsensiSeeder::class,
            PeriodeAkademikSeeder::class,
        ]);

        User::create([
            'nama'     => 'Testing User',
            'username' => 'testing',
            'email'    => 'testing@epiket.com',
            'password' => Hash::make('testing'),
            'status'   => 1,
        ]);

        User::factory(5)->create();
        Guru::factory(5)->create();
        Staff::factory(3)->create();
        Kelas::factory(5)->create();
        Siswa::factory(10)->create();

        $guruIds  = Guru::inRandomOrder()->take(5)->pluck('id');
        $siswaIds = Siswa::inRandomOrder()->take(5)->pluck('id');

        Kelas::all()->each(function ($kelas, $index) use ($guruIds, $siswaIds) {
            $kelas->update([
                'wali_kelas_id'  => $guruIds[$index],
                'ketua_kelas_id' => $siswaIds[$index],
            ]);
        });

        Organisasi::factory(5)->create();

        $guruUntukOrganisasi = Guru::inRandomOrder()->take(5)->pluck('id');

        Organisasi::all()->each(function ($organisasi, $index) use ($guruUntukOrganisasi) {
            $organisasi->update(['pembina_id' => $guruUntukOrganisasi[$index]]);
        });

        Absensi::factory(5)->create();
        AbsensiDetail::factory(10)->create();
    }
}