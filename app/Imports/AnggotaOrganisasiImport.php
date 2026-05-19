<?php

namespace App\Imports;

use App\Models\Admin\Siswa;
use App\Models\Apps\SiswaOrganisasi;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Throwable;

class AnggotaOrganisasiImport implements ToCollection, WithHeadingRow
{
    public int $imported = 0;
    public int $skipped  = 0;
    public array $errors = [];

    protected int $organisasiId;
    protected string $userInput;

    public function __construct(int $organisasiId, string $userInput)
    {
        $this->organisasiId = $organisasiId;
        $this->userInput    = $userInput;
    }

    public function collection(Collection $rows): void
    {
        if ($rows->isEmpty()) {
            $this->errors[] = 'File tidak berisi data. Pastikan file Excel punya minimal 1 baris data setelah baris header.';
            return;
        }

        // Header bisa "Nama Anggota" atau "Nama Siswa" (slug: nama_anggota / nama_siswa)
        $available = array_keys((array) $rows->first());
        if (!in_array('nama_anggota', $available, true) && !in_array('nama_siswa', $available, true)) {
            $this->errors[] = 'Format file tidak sesuai template. Kolom wajib tidak ditemukan: Nama Anggota (atau Nama Siswa). Pastikan menggunakan template yang benar.';
            return;
        }

        foreach ($rows as $idx => $row) {
            $rowNum = $idx + 2;

            $namaSiswa = trim((string) ($row['nama_anggota'] ?? $row['nama_siswa'] ?? ''));
            $namaKelas = trim((string) ($row['kelas'] ?? ''));

            if ($namaSiswa === '') {
                continue;
            }

            try {
                $query = Siswa::where('status', 1)
                    ->whereRaw('LOWER(nama_siswa) = ?', [strtolower($namaSiswa)]);

                if ($namaKelas !== '') {
                    $query->whereHas('kelas', function ($q) use ($namaKelas) {
                        $q->whereRaw('LOWER(nama_kelas) = ?', [strtolower($namaKelas)]);
                    });
                }

                $siswa = $query->first();
                if (!$siswa) {
                    $this->skipped++;
                    $detail = $namaKelas !== '' ? " (kelas \"{$namaKelas}\")" : '';
                    $this->errors[] = "Baris {$rowNum}: Siswa \"{$namaSiswa}\"{$detail} tidak ditemukan di database.";
                    continue;
                }

                $existing = SiswaOrganisasi::where('organisasi_id', $this->organisasiId)
                    ->where('siswa_id', $siswa->id)
                    ->first();

                if ($existing) {
                    $existing->update([
                        'status'         => 1,
                        'user_update'    => $this->userInput,
                        'tanggal_update' => now(),
                    ]);
                } else {
                    SiswaOrganisasi::create([
                        'organisasi_id' => $this->organisasiId,
                        'siswa_id'      => $siswa->id,
                        'status'        => 1,
                        'user_input'    => $this->userInput,
                        'tanggal_input' => now(),
                    ]);
                }

                $this->imported++;
            } catch (Throwable $e) {
                $this->skipped++;
                $this->errors[] = "Baris {$rowNum}: " . $e->getMessage();
            }
        }
    }
}
