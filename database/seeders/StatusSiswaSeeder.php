<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StatusSiswaSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('status_siswa')->insert([
            ['id'=>1,'nama_status'=>'Aktif','status'=>1],
            ['id'=>2,'nama_status'=>'Nonaktif','status'=>1],
            ['id'=>3,'nama_status'=>'Pindah','status'=>1],
            ['id'=>4,'nama_status'=>'Lulus','status'=>1],
        ]);
    }
}
