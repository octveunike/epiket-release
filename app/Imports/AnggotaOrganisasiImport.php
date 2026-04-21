<?php

namespace App\Imports;

use App\Models\Apps\SiswaOrganisasi;
use App\Models\Admin\Siswa;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;

class AnggotaOrganisasiImport implements ToCollection, WithHeadingRow
{
    protected int $organisasiId;
    protected string $userInput;

    public function __construct(int $organisasiId, string $userInput)
    {
        $this->organisasiId = $organisasiId;
        $this->userInput    = $userInput;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $namaSiswa = $row['nama_anggota'] ?? $row['nama_siswa'] ?? null;
            $namaKelas = $row['kelas'] ?? null;

            if (empty($namaSiswa)) {
                continue;
            }

            $query = Siswa::where('status', 1)
                ->whereRaw('LOWER(nama_siswa) = ?', [strtolower(trim($namaSiswa))]);

            if (!empty($namaKelas)) {
                $query->whereHas('kelas', function ($q) use ($namaKelas) {
                    $q->whereRaw('LOWER(nama_kelas) = ?', [strtolower(trim($namaKelas))]);
                });
            }

            $siswa = $query->first();
            if (!$siswa) {
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
        }
    }
}
