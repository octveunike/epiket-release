<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KelasSeeder extends Seeder
{
    public function run(): void
    {
        $kelas = [];

        // X IPA 1 - 7
        for ($i = 1; $i <= 7; $i++) {
            $kelas[] = [
                'nama_kelas' => "X IPA $i",
                'wali_kelas_id' => $i, // nanti mapping ke guru id
                'ketua_kelas_id' => $i,
                'periode_akademik_id' => 1,
                'status' => 1,
            ];
        }

        // X IPS 1 - 3
        for ($i = 1; $i <= 3; $i++) {
            $kelas[] = [
                'nama_kelas' => "X IPS $i",
                'wali_kelas_id' => $i + 7, // lanjut dari IPA
                'ketua_kelas_id' => $i,
                'periode_akademik_id' => 1,
                'status' => 1,
            ];
        }

        DB::table('kelas')->insert($kelas);
    }
}