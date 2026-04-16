<?php

namespace App\Imports;

use App\Models\Admin\Guru;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class GuruImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // Skip baris kosong
        if (empty($row['nip']) || empty($row['nama_guru'])) {
            return null;
        }

        return new Guru([
            'nip'           => $row['nip'],
            'nama_guru'     => $row['nama_guru'],
            'user_id'       => null,
            'status'        => '1',
            'user_input'    => Auth::id(),
            'tanggal_input' => now(),
        ]);
    }
}