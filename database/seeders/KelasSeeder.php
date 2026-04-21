<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KelasSeeder extends Seeder
{
    public function run(): void
    {
        $data = [];
        $id = 1;

        // Kelas X 1 - 10
        for ($i = 1; $i <= 10; $i++) {
            $data[] = [
                'id' => $id++,
                'nama_kelas' => 'X ' . $i,
                'wali_kelas_id' => null,
                'ketua_kelas_id' => null,
                'periode_akademik_id' => 1,
                'status' => 1,
            ];
        }

        // Kelas XI 1 - 10
        for ($i = 1; $i <= 10; $i++) {
            $data[] = [
                'id' => $id++,
                'nama_kelas' => 'XI ' . $i,
                'wali_kelas_id' => null,
                'ketua_kelas_id' => null,
                'periode_akademik_id' => 1,
                'status' => 1,
            ];
        }

        // Kelas XII 1 - 10
        for ($i = 1; $i <= 10; $i++) {
            $data[] = [
                'id' => $id++,
                'nama_kelas' => 'XII ' . $i,
                'wali_kelas_id' => null,
                'ketua_kelas_id' => null,
                'periode_akademik_id' => 1,
                'status' => 1,
            ];
        }

        DB::table('kelas')->insert($data);
    }
}