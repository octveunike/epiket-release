<?php

namespace App\Imports;

use App\Models\Admin\Guru;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Throwable;

class GuruImport implements ToCollection, WithHeadingRow
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

        // Cek header wajib (kalau user salah upload file / template)
        $available = array_keys($rows->first()->toArray());
        $missing   = array_diff(['nip', 'nama_guru'], $available);
        if (!empty($missing)) {
            $this->errors[] = 'Format file tidak sesuai template. Kolom wajib tidak ditemukan: '
                . implode(', ', $missing) . '. Pastikan kolom NIP dan Nama Guru ada pada baris pertama.';
            return;
        }

        $userId = Auth::id();

        foreach ($rows as $idx => $row) {
            $rowNum = $idx + 2;

            $nip       = trim((string) ($row['nip'] ?? ''));
            $namaGuru  = trim((string) ($row['nama_guru'] ?? ''));

            if ($nip === '' || $namaGuru === '') {
                continue; // baris kosong, skip diam-diam
            }

            try {
                Guru::create([
                    'nip'           => mb_substr($nip, 0, 50),
                    'nama_guru'     => mb_substr($namaGuru, 0, 100),
                    'mata_pelajaran'=> $row['mata_pelajaran'] ?? null,
                    'user_id'       => null,
                    'status'        => '1',
                    'user_input'    => $userId,
                    'tanggal_input' => now(),
                ]);
                $this->imported++;
            } catch (Throwable $e) {
                $this->skipped++;
                $this->errors[] = "Baris {$rowNum}: " . $e->getMessage();
            }
        }
    }
}
