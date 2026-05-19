<?php

namespace App\Http\Controllers\Apps;

use App\Http\Controllers\Controller;
use App\Models\Apps\Kelas;
use App\Models\Apps\Absensi;
use App\Models\Apps\AbsensiDetail;
use App\Models\Apps\Dispensasi;
use App\Models\Apps\Keterlambatan;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LaporanExport;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $lockedKelasId = $this->lockedKelasIdForUser();

        $kelasList = $lockedKelasId
            ? Kelas::where('status', 1)->where('id', $lockedKelasId)->get()
            : Kelas::where('status', 1)->orderBy('nama_kelas')->get();

        $rows = $this->buildRows($request);

        return view('Laporan.index', compact('kelasList', 'rows'));
    }

    private function lockedKelasIdForUser(): ?int
    {
        $user = auth()->user();
        if ($user->hasRole('Ketua Kelas')) {
            return $user->ketuaKelasId();
        }
        if ($user->hasRole('Wali Kelas')) {
            return $user->waliKelasId();
        }
        return null;
    }

    public function export(Request $request)
    {
        $rows     = $this->buildRows($request);
        $filename = 'Laporan_Kehadiran_' . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(new LaporanExport($rows, $request->all()), $filename);
    }

    private function buildRows(Request $request): array
    {
        $dari     = $request->filled('dari')     ? Carbon::parse($request->dari)->startOfDay()  : null;
        $sampai   = $request->filled('sampai')   ? Carbon::parse($request->sampai)->endOfDay()  : null;
        $kelasId  = $request->filled('kelas_id') ? (int) $request->kelas_id : null;
        $kategori = $request->kategori;
        $nama     = $request->nama;

        $lockedKelasId = $this->lockedKelasIdForUser();
        if ($lockedKelasId !== null) {
            $kelasId = $lockedKelasId;
        } elseif (auth()->user()->hasRole(['Ketua Kelas', 'Wali Kelas'])) {
            return [];
        }

        $rows = [];

        // 1. Absensi per siswa (hanya yang Disetujui)
        // Kategori 'absensi' = semua non-dispen/terlambat
        // Kategori 'alpha'/'sakit'/'izin' = subset absensi berdasarkan status_absensi_id
        $absensiIds = ['alpha' => 3, 'sakit' => 2, 'izin' => 1];
        $isAbsensiKategori = !$kategori || $kategori === 'absensi' || array_key_exists($kategori, $absensiIds);

        if ($isAbsensiKategori) {
            $query = AbsensiDetail::with(['absensi.kelas', 'siswa', 'statusAbsensi'])
                ->where('status', 1)
                ->whereNotNull('status_absensi_id')
                ->whereNotIn('status_absensi_id', [4, 5])
                ->whereHas('absensi', function ($q) use ($dari, $sampai, $kelasId) {
                    $q->where('status', 1)->where('status_validasi_id', 5);
                    if ($dari)    $q->whereDate('tanggal', '>=', $dari);
                    if ($sampai)  $q->whereDate('tanggal', '<=', $sampai);
                    if ($kelasId) $q->where('kelas_id', $kelasId);
                });

            // Filter spesifik alpha/sakit/izin
            if (array_key_exists($kategori, $absensiIds)) {
                $query->where('status_absensi_id', $absensiIds[$kategori]);
            }

            if ($nama) {
                $query->whereHas('siswa', fn($q) => $q->where('nama_siswa', 'like', "%{$nama}%"));
            }
            foreach ($query->get() as $detail) {
                $tgl    = Carbon::parse($detail->absensi->tanggal)->locale('id');
                $user   = User::find($detail->absensi->user_update ?? $detail->absensi->user_input);
                $rows[] = [
                    'tanggal_sort' => $tgl->timestamp,
                    'hari'         => $tgl->translatedFormat('l'),
                    'tanggal'      => $tgl->translatedFormat('d F Y'),
                    'nama_siswa'   => $detail->siswa->nama_siswa ?? '—',
                    'kelas'        => $detail->absensi->kelas->nama_kelas ?? '—',
                    'kategori'     => $detail->statusAbsensi->keterangan ?? 'Absensi',
                    'deskripsi'    => $detail->keterangan ?? '—',
                    'penginput'    => $user->nama ?? 'Auto',
                ];
            }
        }

        // 2. Keterlambatan
        if (!$kategori || $kategori === 'keterlambatan') {
            $query = Keterlambatan::with(['siswa', 'absensi.kelas'])
                ->where('status', 1)
                ->whereHas('absensi', function ($q) use ($dari, $sampai, $kelasId) {
                    $q->where('status', 1)->where('status_validasi_id', 5);
                    if ($dari)    $q->whereDate('tanggal', '>=', $dari);
                    if ($sampai)  $q->whereDate('tanggal', '<=', $sampai);
                    if ($kelasId) $q->where('kelas_id', $kelasId);
                });
            if ($nama) {
                $query->whereHas('siswa', fn($q) => $q->where('nama_siswa', 'like', "%{$nama}%"));
            }
            foreach ($query->get() as $kt) {
                $tgl    = Carbon::parse($kt->absensi->tanggal)->locale('id');
                $user   = User::find($kt->user_update ?? $kt->user_input);
                $rows[] = [
                    'tanggal_sort' => $tgl->timestamp,
                    'hari'         => $tgl->translatedFormat('l'),
                    'tanggal'      => $tgl->translatedFormat('d F Y'),
                    'nama_siswa'   => $kt->siswa->nama_siswa ?? '—',
                    'kelas'        => $kt->absensi->kelas->nama_kelas ?? '—',
                    'kategori'     => 'Keterlambatan',
                    'deskripsi'    => 'Terlambat — masuk ' . Carbon::parse($kt->waktu_masuk)->format('H:i'),
                    'penginput'    => $user->nama ?? 'Auto',
                ];
            }
        }

        // 3. Dispensasi
        if (!$kategori || $kategori === 'dispensasi') {
            $query = Dispensasi::with(['details.siswa.kelas'])
                ->where('status', 1)
                ->whereHas('details', fn($q) => $q->where('status', 1));

            if ($dari)   $query->where('waktu_mulai', '>=', $dari);
            if ($sampai) $query->where('waktu_mulai', '<=', $sampai);

            foreach ($query->get() as $disp) {
                foreach ($disp->details->where('status', 1) as $detail) {
                    $siswa = $detail->siswa;
                    if (!$siswa) continue;

                    // Filter kelas lewat siswa
                    if ($kelasId && $siswa->kelas_id !== $kelasId) continue;

                    // Filter nama
                    if ($nama && stripos($siswa->nama_siswa ?? '', $nama) === false) continue;

                    $tgl    = Carbon::parse($disp->waktu_mulai)->locale('id');
                    $user   = User::find($disp->user_update ?? $disp->user_input);
                    $rows[] = [
                        'tanggal_sort' => $tgl->timestamp,
                        'hari'         => $tgl->translatedFormat('l'),
                        'tanggal'      => $tgl->translatedFormat('d F Y'),
                        'nama_siswa'   => $siswa->nama_siswa ?? '—',
                        'kelas'        => $siswa->kelas->nama_kelas ?? '—',
                        'kategori'     => 'Dispensasi',
                        'deskripsi'    => $disp->kegiatan ?? '—',
                        'penginput'    => $user->nama ?? 'Auto',
                    ];
                }
            }
        }

        usort($rows, fn($a, $b) => $b['tanggal_sort'] <=> $a['tanggal_sort']);

        return $rows;
    }
}