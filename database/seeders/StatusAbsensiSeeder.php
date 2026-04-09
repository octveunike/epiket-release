<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StatusAbsensiSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('status_absensi')->insert([
            ['id'=>1,'keterangan'=>'Izin','status'=>1],
            ['id'=>2,'keterangan'=>'Sakit','status'=>1],
            ['id'=>3,'keterangan'=>'Alpha','status'=>1],
            ['id'=>4,'keterangan'=>'Dispen','status'=>1],
            ['id'=>5,'keterangan'=>'Terlambat','status'=>1],
        ]);
    }
}
