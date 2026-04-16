<?php

namespace App\Imports;

use App\Models\Admin\Staff;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StaffImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // Skip baris kosong
        if (empty($row['nama_staff'])) {
            return null;
        }

        return new Staff([
            'nama_staff'    => $row['nama_staff'],
            'user_id'       => null,
            'status'        => '1',
            'user_input'    => Auth::id(),
            'tanggal_input' => now(),
        ]);
    }
}