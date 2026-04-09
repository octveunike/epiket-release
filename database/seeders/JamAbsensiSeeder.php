<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JamAbsensiSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('jam_absensi')->insert([
            ['id'=>1,'jam_ke'=>1,'waktu_mulai'=>'06:30:00','waktu_selesai'=>'07:15:00','keterangan'=>'Jam ke-1','status'=>1],
            ['id'=>2,'jam_ke'=>2,'waktu_mulai'=>'07:15:00','waktu_selesai'=>'08:00:00','keterangan'=>'Jam ke-2','status'=>1],
            ['id'=>3,'jam_ke'=>3,'waktu_mulai'=>'08:00:00','waktu_selesai'=>'08:45:00','keterangan'=>'Jam ke-3','status'=>1],
            ['id'=>4,'jam_ke'=>4,'waktu_mulai'=>'08:45:00','waktu_selesai'=>'09:30:00','keterangan'=>'Jam ke-4','status'=>1],
        ]);
    }
}
