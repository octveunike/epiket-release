<?php

namespace App\Imports;

use App\Models\Apps\Organisasi;
use App\Models\Admin\Guru;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class OrganisasiImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        if (empty($row['nama_organisasi'])) {
            return null;
        }

        $pembinaId = null;
        $namaPembina = $row['nama_guru_pembina'] ?? null;
        if (!empty($namaPembina)) {
            $pembinaId = Guru::where('status', 1)
                ->whereRaw('LOWER(nama_guru) = ?', [strtolower(trim($namaPembina))])
                ->value('id');
        }

        $user = auth()->user();
        $userInput = $user->username ?? $user->nama ?? $user->email;

        return new Organisasi([
            'nama_organisasi' => $row['nama_organisasi'],
            'pembina_id'      => $pembinaId,
            'keterangan'      => $row['keterangan'] ?? null,
            'status'          => 1,
            'user_input'      => $userInput,
            'tanggal_input'   => now(),
        ]);
    }
}
