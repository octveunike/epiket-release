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
                'id'=>1,
                'nama_periode'=>'2024/2025 Ganjil',
                'semester'=>'Ganjil',
                'status'=>1,
                'tahun_ajaran'=>'2024/2025',
                'tanggal_mulai'=>'2024-02-01',
                'tanggal_selesai'=>'2024-07-02'
            ],
            [
                'id'=>2,
                'nama_periode'=>'2024/2025 Genap',
                'semester'=>'Genap',
                'status'=>0,
                'tahun_ajaran'=>'2024/2025',
                'tanggal_mulai'=>'2025-02-01',
                'tanggal_selesai'=>'2025-08-01'
            ],
            [
                'id'=>3,
                'nama_periode'=>'2024/2025 Ganjil',
                'semester'=>'Ganjil',
                'status'=>1,
                'tahun_ajaran'=>'2024/2025',
                'tanggal_mulai'=>'2026-03-01',
                'tanggal_selesai'=>'2026-03-31'
            ]
        ]);
    }
}
