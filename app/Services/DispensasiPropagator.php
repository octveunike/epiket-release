<?php

namespace App\Services;

use App\Models\Admin\Siswa;
use App\Models\Apps\Absensi;
use App\Models\Apps\AbsensiDetail;
use App\Models\Apps\Dispensasi;
use App\Models\Apps\DispensasiDetail;
use App\Models\Reference\StatusValidasi;
use Carbon\Carbon;

class DispensasiPropagator
{
    /**
     * For a freshly created Absensi row, insert AbsensiDetail rows
     * (status Dispen = 4) for every student covered by an approved
     * Dispensasi whose date range includes the Absensi's tanggal.
     *
     * Idempotent: existing active AbsensiDetail rows are left untouched.
     * Returns the number of details inserted.
     */
    public static function applyApprovedDispensasiToAbsensi(Absensi $absensi, string|int $userInput = 'system'): int
    {
        if (!$absensi->kelas_id || !$absensi->tanggal) {
            return 0;
        }

        $tanggal = Carbon::parse($absensi->tanggal)->toDateString();

        $statusDisetujuiId = StatusValidasi::where('nama_status', 'Disetujui')
            ->where('status', 1)
            ->value('id');
        if (!$statusDisetujuiId) {
            return 0;
        }

        $dispensasiList = Dispensasi::where('status', 1)
            ->where('status_validasi_id', $statusDisetujuiId)
            ->whereDate('waktu_mulai', '<=', $tanggal)
            ->whereDate('waktu_selesai', '>=', $tanggal)
            ->get(['id', 'kegiatan']);

        if ($dispensasiList->isEmpty()) {
            return 0;
        }

        $siswaIdsInKelas = Siswa::where('status', 1)
            ->where('kelas_id', $absensi->kelas_id)
            ->pluck('id')
            ->all();
        if (empty($siswaIdsInKelas)) {
            return 0;
        }

        $details = DispensasiDetail::whereIn('dispensasi_id', $dispensasiList->pluck('id'))
            ->whereIn('siswa_id', $siswaIdsInKelas)
            ->where('status', 1)
            ->get(['siswa_id', 'dispensasi_id']);

        $kegiatanByDispensasi = $dispensasiList->pluck('kegiatan', 'id');

        $existingSiswaIds = AbsensiDetail::where('absensi_id', $absensi->id)
            ->where('status', 1)
            ->pluck('siswa_id')
            ->map(fn ($v) => (int) $v)
            ->all();

        $applied = 0;
        foreach ($details as $d) {
            if (in_array((int) $d->siswa_id, $existingSiswaIds, true)) {
                continue;
            }

            AbsensiDetail::create([
                'absensi_id'        => $absensi->id,
                'siswa_id'          => $d->siswa_id,
                'status_absensi_id' => 4,
                'is_full_day'       => 1,
                'keterangan'        => 'Dispensasi: ' . ($kegiatanByDispensasi[$d->dispensasi_id] ?? '-'),
                'status'            => 1,
                'user_input'        => $userInput,
                'tanggal_input'     => now(),
            ]);

            $existingSiswaIds[] = (int) $d->siswa_id;
            $applied++;
        }

        return $applied;
    }
}
