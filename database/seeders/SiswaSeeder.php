<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SiswaSeeder extends Seeder
{
    public function run(): void
    {
        for ($i = 1; $i <= 10; $i++) {
            DB::table('siswa')->insert([
                'id' => $i,
                'nama_siswa' => "Ketua Kelas $i",
                'nis' => str_pad($i, 10, '0', STR_PAD_LEFT),
                'status' => 1,
            ]);
        }
    }
}