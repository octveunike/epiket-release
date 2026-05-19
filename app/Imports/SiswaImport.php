<?php

namespace App\Imports;

use App\Models\Admin\Siswa;
use App\Models\Apps\Kelas;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Throwable;

class SiswaImport implements ToCollection, WithHeadingRow
{
    public int $imported = 0;
    public int $skipped  = 0;
    public array $errors = [];

    public function collection(Collection $rows): void
    {
        if ($rows->isEmpty()) {
            $this->errors[] = 'File tidak berisi data. Pastikan file Excel punya minimal 1 baris data setelah baris header.';
            return;
        }

        $available = array_keys((array) $rows->first());
        $missing   = array_diff(['nis', 'nama_siswa', 'kelas'], $available);
        if (!empty($missing)) {
            $this->errors[] = 'Format file tidak sesuai template. Kolom wajib tidak ditemukan: '
                . implode(', ', $missing) . '. Pastikan kolom NIS, Nama Siswa, dan Kelas ada pada baris pertama.';
            return;
        }

        $userId = Auth::id();

        foreach ($rows as $idx => $row) {
            $rowNum = $idx + 2;

            $nis        = trim((string) ($row['nis'] ?? ''));
            $namaSiswa  = trim((string) ($row['nama_siswa'] ?? ''));
            $namaKelas  = trim((string) ($row['kelas'] ?? ''));

            if ($nis === '' || $namaSiswa === '') {
                continue;
            }

            // Resolve kelas
            $kelas = null;
            if ($namaKelas !== '') {
                $key = strtolower($namaKelas);
                $key = str_replace('mipa', 'ipa', $key);
                $key = preg_replace('/\s+/', ' ', $key);
                $kelas = Kelas::whereRaw('LOWER(nama_kelas) = ?', [$key])->first();
            }

            if (!$kelas) {
                $this->skipped++;
                $this->errors[] = "Baris {$rowNum}: Kelas \"{$namaKelas}\" tidak ditemukan di database.";
                continue;
            }

            // Parse tanggal
            $tanggal = null;
            if (!empty($row['tanggal_masuk'])) {
                try {
                    if (is_numeric($row['tanggal_masuk'])) {
                        $tanggal = Date::excelToDateTimeObject($row['tanggal_masuk'])->format('Y-m-d');
                    } else {
                        try {
                            $tanggal = Carbon::createFromFormat('d-m-Y', $row['tanggal_masuk'])->format('Y-m-d');
                        } catch (\Exception $e) {
                            $tanggal = date('Y-m-d', strtotime($row['tanggal_masuk']));
                        }
                    }
                } catch (Throwable $e) {
                    $this->skipped++;
                    $this->errors[] = "Baris {$rowNum}: Format tanggal masuk tidak valid (\"{$row['tanggal_masuk']}\").";
                    continue;
                }
            }

            try {
                Siswa::create([
                    'nis'             => mb_substr($nis, 0, 50),
                    'nama_siswa'      => mb_substr($namaSiswa, 0, 100),
                    'jenis_kelamin'   => $row['jenis_kelamin'] ?? null,
                    'tanggal_masuk'   => $tanggal,
                    'kelas_id'        => $kelas->id,
                    'status_siswa_id' => $row['status_siswa_id'] ?? 1,
                    'status'          => 1,
                    'user_input'      => $userId,
                    'tanggal_input'   => now(),
                ]);
                $this->imported++;
            } catch (Throwable $e) {
                $this->skipped++;
                $this->errors[] = "Baris {$rowNum}: " . $e->getMessage();
            }
        }
    }
}
