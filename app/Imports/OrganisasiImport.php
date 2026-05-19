<?php

namespace App\Imports;

use App\Models\Apps\Organisasi;
use App\Models\Admin\Guru;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Throwable;

class OrganisasiImport implements ToCollection, WithHeadingRow
{
    public int $imported = 0;
    public int $skipped  = 0;
    public array $errors = [];

    public function collection(Collection $rows)
    {
        $user      = auth()->user();
        $userInput = $user->username ?? $user->nama ?? $user->email ?? 'system';

        foreach ($rows as $idx => $row) {
            $rowNumber = $idx + 2; // +1 untuk heading row, +1 karena Excel 1-indexed

            $namaOrg = trim((string) ($row['nama_organisasi'] ?? ''));
            if ($namaOrg === '') {
                // Baris kosong / heading-only → skip diam-diam
                continue;
            }

            try {
                // Lookup pembina by nama_guru
                $pembinaId   = null;
                $namaPembina = trim((string) ($row['nama_guru_pembina'] ?? ''));
                if ($namaPembina !== '') {
                    $pembinaId = Guru::where('status', 1)
                        ->whereRaw('LOWER(nama_guru) = ?', [strtolower($namaPembina)])
                        ->value('id');

                    if ($pembinaId === null) {
                        $this->errors[] = "Baris {$rowNumber}: Guru pembina \"{$namaPembina}\" tidak ditemukan, akan diisi kosong.";
                    }
                }

                // Header template default: "Deskripsi/Keterangan (Opsional)" → Str::slug
                // me-strip "/" jadi keys yang dihasilkan: `deskripsiketerangan_opsional`.
                // Sediakan fallback untuk varian header lain (kalau template kelak diubah).
                $keterangan = $row['deskripsiketerangan_opsional']
                    ?? $row['deskripsiketerangan']
                    ?? $row['deskripsi_keterangan_opsional']
                    ?? $row['deskripsi_keterangan']
                    ?? $row['keterangan_opsional']
                    ?? $row['keterangan']
                    ?? $row['deskripsi']
                    ?? null;

                if (is_string($keterangan)) {
                    $keterangan = trim($keterangan);
                    if ($keterangan === '') {
                        $keterangan = null;
                    } elseif (mb_strlen($keterangan) > 255) {
                        // Kolom keterangan VARCHAR(255). Truncate supaya tidak gagal insert.
                        $keterangan = mb_substr($keterangan, 0, 255);
                    }
                }

                Organisasi::create([
                    'nama_organisasi' => mb_substr($namaOrg, 0, 100),
                    'pembina_id'      => $pembinaId,
                    'keterangan'      => $keterangan,
                    'status'          => 1,
                    'user_input'      => $userInput,
                    'tanggal_input'   => now(),
                ]);

                $this->imported++;
            } catch (Throwable $e) {
                $this->skipped++;
                $this->errors[] = "Baris {$rowNumber}: " . $e->getMessage();
            }
        }
    }
}
