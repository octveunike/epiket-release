<?php

namespace App\Imports;

use App\Models\Admin\Staff;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Throwable;

class StaffImport implements ToCollection, WithHeadingRow
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

        $available = array_keys($rows->first()->toArray());
        $missing   = array_diff(['nama_staff'], $available);
        if (!empty($missing)) {
            $this->errors[] = 'Format file tidak sesuai template. Kolom wajib tidak ditemukan: '
                . implode(', ', $missing) . '. Pastikan kolom Nama Staff ada pada baris pertama.';
            return;
        }

        $userId = Auth::id();

        foreach ($rows as $idx => $row) {
            $rowNum = $idx + 2;

            $namaStaff = trim((string) ($row['nama_staff'] ?? ''));
            if ($namaStaff === '') {
                continue;
            }

            try {
                Staff::create([
                    'nama_staff'    => mb_substr($namaStaff, 0, 100),
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
