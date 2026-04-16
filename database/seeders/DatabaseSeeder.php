<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Admin\Staff;
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
            GuruSeeder::class,
            SiswaSeeder::class,
            KelasSeeder::class,
        ]);

        User::create([
            'nama'     => 'Testing User',
            'username' => 'testing',
            'email'    => 'testing@epiket.com',
            'password' => Hash::make('testing'),
            'status'   => 1,
        ]);

        Staff::factory(3)->create();

        Organisasi::factory(5)->create();

        $guruUntukOrganisasi = \App\Models\Admin\Guru::inRandomOrder()->take(5)->pluck('id');

        Organisasi::all()->each(function ($organisasi, $index) use ($guruUntukOrganisasi) {
            $organisasi->update([
                'pembina_id' => $guruUntukOrganisasi[$index] ?? null
            ]);
        });

        Absensi::factory(5)->create();
        AbsensiDetail::factory(10)->create();

        \App\Models\Apps\DaftarTamu::factory(10)->create();
    }
}