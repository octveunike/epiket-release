<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StatusVerifikasiSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('status_verifikasi')->insert([
            ['id'=>1,'nama_status'=>'Menunggu Pengisian','keterangan'=>'Menunggu pengisian','status'=>1],
            ['id'=>2,'nama_status'=>'Menunggu Piket','keterangan'=>'Menunggu verifikasi piket','status'=>1],
            ['id'=>3,'nama_status'=>'Menunggu Wali','keterangan'=>'Menunggu wali kelas','status'=>1],
            ['id'=>4,'nama_status'=>'Menunggu Pembina','keterangan'=>'Menunggu pembina','status'=>1],
            ['id'=>5,'nama_status'=>'Disetujui','keterangan'=>'Disetujui','status'=>1],
            ['id'=>6,'nama_status'=>'Ditolak','keterangan'=>'Ditolak','status'=>1],
        ]);
    }
}
