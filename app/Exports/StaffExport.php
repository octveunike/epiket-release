<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

/**
 * Backup data Staf. Header baris-1 sama dengan template import (Nama Staff)
 * agar hasil unduhan bisa di-import kembali.
 */
class StaffExport implements FromArray, WithHeadings, WithTitle, ShouldAutoSize
{
    public function title(): string
    {
        return 'Data Staf';
    }

    public function headings(): array
    {
        return ['Nama Staff'];
    }

    public function array(): array
    {
        return DB::table('staff')
            ->where('status', 1)
            ->orderBy('nama_staff')
            ->get(['nama_staff'])
            ->map(fn ($s) => [$s->nama_staff])
            ->toArray();
    }
}
