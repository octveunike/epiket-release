<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PeriodeAkademikSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('periode_akademik')->insert([
            [
                'id' => 1,
                'nama_periode' => '2025/2026 Ganjil',
                'tahun_ajaran' => '2025/2026',
                'semester' => 'Ganjil',
                'tanggal_mulai' => '2025-07-01',
                'tanggal_selesai' => '2025-12-31',
                'status' => 0,
            ],
            [
                'id' => 2,
                'nama_periode' => '2025/2026 Genap',
                'tahun_ajaran' => '2025/2026',
                'semester' => 'Genap',
                'tanggal_mulai' => '2026-01-01',
                'tanggal_selesai' => '2026-06-30',
                'status' => 1,
            ],
        ]);
    }
}