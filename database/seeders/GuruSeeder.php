<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GuruSeeder extends Seeder
{
    public function run(): void
    {
        for ($i = 1; $i <= 13; $i++) {
            DB::table('guru')->insert([
                'id' => $i,
                'nama_guru' => "Guru $i",
                'nip' => str_pad($i, 18, '0', STR_PAD_LEFT),
                'status' => 1,
            ]);
        }
    }
}