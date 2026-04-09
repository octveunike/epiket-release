<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('roles')->insert([
            ['id'=>1,'nama_role'=>'Admin','keterangan'=>'Pengelola sistem','status'=>1],
            ['id'=>2,'nama_role'=>'Petugas Piket','keterangan'=>'Verifikasi Absensi','status'=>1],
            ['id'=>3,'nama_role'=>'Wali Kelas','keterangan'=>'Wali Kelas','status'=>1],
            ['id'=>4,'nama_role'=>'Pembina Ekskul','keterangan'=>'Validasi Dispensasi','status'=>1],
            ['id'=>5,'nama_role'=>'Guru','keterangan'=>'Guru Mata Pelajaran','status'=>1],
            ['id'=>6,'nama_role'=>'Komdis','keterangan'=>'OSIS pencatat keterlambatan','status'=>1],
            ['id'=>7,'nama_role'=>'Ketua Kelas','keterangan'=>'Input Absensi','status'=>1],
            ['id'=>8,'nama_role'=>'Siswa','keterangan'=>'Siswa sekolah','status'=>1],
        ]);
    }
}