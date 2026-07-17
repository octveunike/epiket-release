<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

/**
 * Backup data Siswa. Header baris-1 sama dengan template import
 * (NIS, Nama Siswa, Jenis Kelamin, Kelas, Tanggal Masuk, Status Siswa Id)
 * agar hasil unduhan bisa di-import kembali.
 */
class SiswaExport implements FromArray, WithHeadings, WithTitle, ShouldAutoSize
{
    public function title(): string
    {
        return 'Data Siswa';
    }

    public function headings(): array
    {
        return ['NIS', 'Nama Siswa', 'Jenis Kelamin', 'Kelas', 'Tanggal Masuk', 'Status Siswa Id'];
    }

    public function array(): array
    {
        return DB::table('siswa as s')
            ->leftJoin('kelas as k', 'k.id', '=', 's.kelas_id')
            ->where('s.status', 1)
            ->orderBy('s.nama_siswa')
            ->get(['s.nis', 's.nama_siswa', 's.jenis_kelamin', 'k.nama_kelas as kelas', 's.tanggal_masuk', 's.status_siswa_id'])
            ->map(fn ($s) => [$s->nis, $s->nama_siswa, $s->jenis_kelamin, $s->kelas, $s->tanggal_masuk, $s->status_siswa_id])
            ->toArray();
    }
}
