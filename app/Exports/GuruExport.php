<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

/**
 * Backup data Guru. Header baris-1 sengaja dibuat sama dengan template import
 * (NIP, Nama Guru, Mata Pelajaran) agar file hasil unduhan bisa di-import kembali.
 */
class GuruExport implements FromArray, WithHeadings, WithTitle, ShouldAutoSize
{
    public function title(): string
    {
        return 'Data Guru';
    }

    public function headings(): array
    {
        return ['NIP', 'Nama Guru', 'Mata Pelajaran'];
    }

    public function array(): array
    {
        return DB::table('guru')
            ->where('status', 1)
            ->orderBy('nama_guru')
            ->get(['nip', 'nama_guru', 'mata_pelajaran'])
            ->map(fn ($g) => [$g->nip, $g->nama_guru, $g->mata_pelajaran])
            ->toArray();
    }
}
