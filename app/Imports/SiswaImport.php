<?php

namespace App\Imports;

use App\Models\Admin\Siswa;
use App\Models\Apps\Kelas;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Carbon\Carbon;

class SiswaImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        if (empty($row['nis']) || empty($row['nama_siswa'])) {
            return null;
        }

        $kelas = null;

        if (!empty($row['kelas'])) {
            $namaKelas = strtolower(trim($row['kelas']));

            $namaKelas = str_replace('mipa', 'ipa', $namaKelas);
            $namaKelas = preg_replace('/\s+/', ' ', $namaKelas);

            $kelas = Kelas::whereRaw('LOWER(nama_kelas) = ?', [$namaKelas])->first();
        }

        if (!$kelas) {
            return null;
        }

        $tanggal = null;

        if (!empty($row['tanggal_masuk'])) {
            if (is_numeric($row['tanggal_masuk'])) {
                $tanggal = Date::excelToDateTimeObject($row['tanggal_masuk'])->format('Y-m-d');
            } else {
                try {
                    $tanggal = Carbon::createFromFormat('d-m-Y', $row['tanggal_masuk'])->format('Y-m-d');
                } catch (\Exception $e) {
                    $tanggal = date('Y-m-d', strtotime($row['tanggal_masuk']));
                }
            }
        }

        return new Siswa([
            'nis'               => $row['nis'],
            'nama_siswa'        => $row['nama_siswa'],
            'jenis_kelamin'     => $row['jenis_kelamin'] ?? null,
            'tanggal_masuk'     => $tanggal,
            'kelas_id'          => $kelas->id,
            'status_siswa_id'   => $row['status_siswa_id'] ?? 1,
            'status'            => 1,
            'user_input'        => Auth::id(),
            'tanggal_input'     => now(),
        ]);
    }
}