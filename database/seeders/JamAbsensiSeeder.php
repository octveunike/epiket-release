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
            ['id'=>5,'jam_ke'=>5,'waktu_mulai'=>'09:45:00','waktu_selesai'=>'10:30:00','keterangan'=>'Jam ke-5','status'=>1],
            ['id'=>6,'jam_ke'=>6,'waktu_mulai'=>'10:30:00','waktu_selesai'=>'11:15:00','keterangan'=>'Jam ke-6','status'=>1],
            ['id'=>7,'jam_ke'=>7,'waktu_mulai'=>'12:00:00','waktu_selesai'=>'12:45:00','keterangan'=>'Jam ke-7','status'=>1],
            ['id'=>8,'jam_ke'=>8,'waktu_mulai'=>'12:45:00','waktu_selesai'=>'13:30:00','keterangan'=>'Jam ke-8','status'=>1],
            ['id'=>9,'jam_ke'=>9,'waktu_mulai'=>'13:30:00','waktu_selesai'=>'14:15:00','keterangan'=>'Jam ke-9','status'=>1],
            ['id'=>10,'jam_ke'=>10,'waktu_mulai'=>'14:15:00','waktu_selesai'=>'15:00:00','keterangan'=>'Jam ke-10','status'=>1],
            
        ]);
    }
}
