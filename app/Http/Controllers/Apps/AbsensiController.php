<?php

namespace App\Http\Controllers\Apps;

use App\Http\Controllers\Controller;
use App\Models\Admin\Siswa;
use App\Models\Apps\Kelas;
use App\Models\Apps\Absensi;
use App\Models\Apps\AbsensiDetail;
use App\Models\Apps\AbsensiDetailJam;
use App\Models\Apps\PeriodeAkademik;
use App\Models\Reference\JamAbsensi;
use App\Models\Reference\StatusValidasi;
use App\Services\DispensasiPropagator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;

class AbsensiController extends Controller
{
    public function index(Request $request)
    {
        // Wali Kelas tidak punya akses ke index umum, redirect ke halaman validasinya
        if (auth()->user()->hasRole('Wali Kelas')) {
            return redirect()->route('Absensi.validasi.index');
        }

        $ketuaKelasId = null;
        if (auth()->user()->hasRole('Ketua Kelas')) {
            $ketuaKelasId = auth()->user()->ketuaKelasId();
            if (!$ketuaKelasId) {
                abort(403, 'Akun Anda belum tertaut ke data siswa/kelas. Hubungi Admin.');
            }
        }

        // Default Periode Akademik = periode aktif (status = 1)
        $periodeAktif = PeriodeAkademik::where('status', 1)->first();
        $periodeId    = $request->filled('periode_akademik_id')
                      ? (int) $request->periode_akademik_id
                      : ($periodeAktif?->id);

        // Tabel atas: semua absensi yang belum Disetujui
        $query = Absensi::withCount('details')
            ->with(['kelas', 'periodeAkademik', 'statusValidasi', 'userUpdate', 'userInput'])
            ->where('status', 1)
            ->where('status_validasi_id', '!=', 5)
            ->orderByDesc('tanggal');

        if ($request->filled('dari')) {
            $query->whereDate('tanggal', '>=', $request->dari);
        }
        if ($request->filled('sampai')) {
            $query->whereDate('tanggal', '<=', $request->sampai);
        }

        if ($ketuaKelasId) {
            $query->where('kelas_id', $ketuaKelasId);
        } elseif ($request->filled('kelas_id')) {
            $query->where('kelas_id', $request->kelas_id);
        }

        if ($periodeId) {
            $query->where('periode_akademik_id', $periodeId);
        }

        $absensiList = $query->get();

        // Tabel bawah: history (Disetujui)
        $historyQuery = Absensi::with(['kelas', 'periodeAkademik', 'statusValidasi', 'userUpdate', 'userInput'])
            ->where('status', 1)
            ->where('status_validasi_id', 5)
            ->orderByDesc('tanggal_update');

        if ($request->filled('dari')) {
            $historyQuery->whereDate('tanggal', '>=', $request->dari);
        }
        if ($request->filled('sampai')) {
            $historyQuery->whereDate('tanggal', '<=', $request->sampai);
        }

        if ($ketuaKelasId) {
            $historyQuery->where('kelas_id', $ketuaKelasId);
        } elseif ($request->filled('kelas_id')) {
            $historyQuery->where('kelas_id', $request->kelas_id);
        }

        if ($periodeId) {
            $historyQuery->where('periode_akademik_id', $periodeId);
        }

        $historyList = $historyQuery->get();

        $kelas                     = $ketuaKelasId
            ? Kelas::where('status', 1)->where('id', $ketuaKelasId)->get()
            : Kelas::where('status', 1)->orderBy('nama_kelas')->get();
        $periodeList               = PeriodeAkademik::where('status', 1)->orderByDesc('id')->get();
        $statusMenungguPengisianId = StatusValidasi::where('nama_status', 'Menunggu Pengisian')
                                        ->where('status', 1)->value('id');
        $statusPerluRevisiId       = StatusValidasi::where('nama_status', 'Perlu Revisi')
                                        ->where('status', 1)->value('id');

        return view('Absensi.index', compact('absensiList', 'historyList', 'kelas', 'periodeList', 'periodeId', 'statusMenungguPengisianId', 'statusPerluRevisiId'));
    }

    public function create()
    {
        if (!auth()->user()->hasRole(['Admin', 'Petugas Piket'])) {
            return redirect()->route('Absensi.index');
        }

        $kelas        = Kelas::where('status', 1)->orderBy('nama_kelas')->get();
        $periodeAktif = PeriodeAkademik::where('status', 1)->first();

        return view('Absensi.create', compact('kelas', 'periodeAktif'));
    }

    public function store(Request $request)
    {
        if (!auth()->user()->hasRole(['Admin', 'Petugas Piket'])) {
            abort(403, 'Hanya Admin / Petugas Piket yang dapat membuat absensi baru.');
        }

        $request->validate([
            'kelas_id' => ['required', 'integer'],
            'tanggal'  => ['required', 'date'],
        ]);

        $periodeAktif = PeriodeAkademik::where('status', 1)->first();

        if (!$periodeAktif) {
            return back()->with('error', 'Tidak ada periode akademik aktif.')->withInput();
        }

        $statusBelumDiisi = StatusValidasi::where('nama_status', 'Menunggu Pengisian')
                                ->where('status', 1)->first();

        $absensi = Absensi::create([
            'kelas_id'             => $request->kelas_id,
            'tanggal'              => $request->tanggal,
            'status_validasi_id' => $statusBelumDiisi?->id,
            'periode_akademik_id'  => $periodeAktif->id,
            'status'               => '1',
            'user_input'           => auth()->user()->id,
            'tanggal_input'        => date('Y-m-d H:i:s'),
        ]);

        DispensasiPropagator::applyApprovedDispensasiToAbsensi($absensi, auth()->user()->id);

        return redirect()->route('Absensi.isiAbsensi', $absensi->id)
            ->with('success', 'Data absensi berhasil ditambahkan. Silakan isi absensi.');
    }

    public function generate(Request $request)
    {
        if (!auth()->user()->hasRole(['Admin', 'Petugas Piket'])) {
            abort(403, 'Hanya Admin / Petugas Piket yang dapat menggenerate absensi.');
        }

        $periodeAktif = PeriodeAkademik::where('status', 1)->first();

        if (!$periodeAktif) {
            return back()->with('error', 'Tidak ada periode akademik aktif.');
        }

        $statusBelumDiisi = StatusValidasi::where('nama_status', 'Menunggu Pengisian')
                                ->where('status', 1)->first();

        $kelas = Kelas::where('status', 1)->get();
        $today = date('Y-m-d');
        $count = 0;

        foreach ($kelas as $k) {
            $absensi = Absensi::firstOrCreate(
                [
                    'kelas_id' => $k->id,
                    'tanggal'  => $today,
                ],
                [
                    'status_validasi_id' => $statusBelumDiisi?->id,
                    'periode_akademik_id'  => $periodeAktif->id,
                    'status'               => '1',
                    'user_input'           => auth()->user()->id,
                    'tanggal_input'        => date('Y-m-d H:i:s'),
                ]
            );

            if ($absensi->wasRecentlyCreated) {
                // If there are approved dispensations for today, apply them
                DispensasiPropagator::applyApprovedDispensasiToAbsensi($absensi, auth()->user()->id);
                $count++;
            }
        }

        if ($count > 0) {
            return redirect()->route('Absensi.index')
                ->with('success', "$count data absensi hari ini berhasil digenerate.");
        }

        return redirect()->route('Absensi.index')
            ->with('success', 'Semua data absensi untuk hari ini sudah ada, tidak ada data baru yang dibuat.');
    }

    public function isiAbsensi(string $id)
    {
        $absensi = Absensi::with(['kelas', 'periodeAkademik'])
            ->where('status', 1)
            ->findOrFail($id);

        $this->authorizeKelasAccess($absensi->kelas_id);

        $statusMenungguPengisianId = StatusValidasi::where('nama_status', 'Menunggu Pengisian')
                                        ->where('status', 1)->value('id');

        // Hanya block jika sudah disetujui final (id=5). Status 6 (Perlu Revisi) tetap bisa diedit.
        if ($absensi->status_validasi_id == 5) {
            return redirect()->route('Absensi.show', $id)
                ->with('error', 'Absensi ini sudah final dan tidak dapat diedit.');
        }

        $siswa = Siswa::where('kelas_id', $absensi->kelas_id)
            ->where('status', 1)
            ->orderBy('nama_siswa')
            ->get(['id', 'nama_siswa', 'nis']);

        $sudahTercatat = AbsensiDetail::with(['siswa', 'jams.jam'])
            ->where('absensi_id', $absensi->id)
            ->where('status', 1)
            ->get();

        $jam = JamAbsensi::where('status', 1)->orderBy('jam_ke')->get();

        return view('Absensi.isi', compact('absensi', 'siswa', 'jam', 'sudahTercatat'));
    }

    public function storeDetail(Request $request, string $id)
    {
        $absensi = Absensi::where('status', 1)->findOrFail($id);

        $request->validate([
            'detail'            => ['nullable', 'array'],
            'detail.*.siswa_id' => ['required_with:detail', 'integer'],
        ]);

        DB::beginTransaction();
        try {
            foreach ($request->input('detail', []) as $d) {
                $statusAbsensiId = isset($d['status_absensi_id']) && $d['status_absensi_id'] !== ''
                    ? (int) $d['status_absensi_id']
                    : null;

                $isFullDay = isset($d['is_full_day']) ? (int) $d['is_full_day'] : 1;

                $detail = AbsensiDetail::updateOrCreate(
                    [
                        'absensi_id' => $absensi->id,
                        'siswa_id'   => (int) $d['siswa_id'],
                    ],
                    [
                        'is_full_day'       => $isFullDay,
                        'status_absensi_id' => $statusAbsensiId,
                        'keterangan'        => $d['keterangan'] ?? null,
                        'lampiran_absensi'  => null,
                        'status'            => '1',
                        'user_input'        => auth()->user()->id,
                        'tanggal_input'     => date('Y-m-d H:i:s'),
                    ]
                );

                $jams = $d['jams'] ?? [];
                if (!$isFullDay && !empty($jams)) {
                    $detail->jams()->where('status', 1)->update([
                        'status'         => 9,
                        'user_update'    => auth()->user()->id,
                        'tanggal_update' => date('Y-m-d H:i:s'),
                    ]);
                    foreach ($jams as $jamId) {
                        AbsensiDetailJam::create([
                            'absensi_detail_id' => $detail->id,
                            'jam_ke_id'         => $jamId,
                            'status'            => '1',
                            'user_input'        => auth()->user()->id,
                            'tanggal_input'     => date('Y-m-d H:i:s'),
                        ]);
                    }
                }
            }

            $absensi->update([
                'status_validasi_id' => 3,
                'user_update'          => auth()->user()->id,
                'tanggal_update'       => date('Y-m-d H:i:s'),
            ]);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menyimpan: ' . $e->getMessage());
        }

        return redirect()->route('Absensi.index')
            ->with('success', 'Absensi berhasil disimpan.');
    }

    public function show(string $id)
    {
        $absensi = Absensi::with([
            'kelas',
            'periodeAkademik',
            'statusValidasi',
            'userInput',
            'details.siswa',
            'details.statusAbsensi',
            'details.jams.jam',
        ])->where('status', 1)->findOrFail($id);

        $this->authorizeKelasAccess($absensi->kelas_id);

        return view('Absensi.show', compact('absensi'));
    }

    private function authorizeKelasAccess(?int $kelasId): void
    {
        $user = auth()->user();
        if ($user->hasRole('Ketua Kelas')) {
            $allowed = $user->ketuaKelasId();
            if (!$allowed || $allowed !== (int) $kelasId) {
                abort(403, 'Anda hanya dapat mengakses data kelas Anda sendiri.');
            }
        }
        if ($user->hasRole('Wali Kelas')) {
            $allowed = $user->waliKelasId();
            if (!$allowed || $allowed !== (int) $kelasId) {
                abort(403, 'Anda hanya dapat mengakses data kelas Anda sendiri.');
            }
        }
    }

    public function destroy(string $id)
    {
        $absensi = Absensi::where('status', 1)->findOrFail($id);

        DB::beginTransaction();
        try {
            $absensi->details()->where('status', 1)->each(function ($detail) {
                $detail->jams()->where('status', 1)->update([
                    'status'         => 9,
                    'user_update'    => auth()->user()->id,
                    'tanggal_update' => date('Y-m-d H:i:s'),
                ]);
                $detail->update([
                    'status'         => 9,
                    'user_update'    => auth()->user()->id,
                    'tanggal_update' => date('Y-m-d H:i:s'),
                ]);
            });

            $absensi->update([
                'status'         => 9,
                'user_update'    => auth()->user()->id,
                'tanggal_update' => date('Y-m-d H:i:s'),
            ]);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menghapus: ' . $e->getMessage());
        }

        return redirect()->route('Absensi.index')
            ->with('success', 'Data absensi berhasil dihapus.');
    }

    // ── WALI KELAS / PETUGAS PIKET: Index (daftar menunggu validasi) ──────────

    public function waliKelasIndex(Request $request)
    {
        $user         = auth()->user();
        $scopeKelasId = $this->resolveValidationScopeKelasId();

        // Default Periode Akademik = periode aktif (status = 1)
        $periodeAktif = PeriodeAkademik::where('status', 1)->first();
        $periodeId    = $request->filled('periode_akademik_id')
                      ? (int) $request->periode_akademik_id
                      : ($periodeAktif?->id);

        // Tabel atas: absensi menunggu validasi
        $query = Absensi::with(['kelas', 'periodeAkademik', 'statusValidasi', 'userUpdate', 'userInput'])
            ->where('status', 1)
            ->where('status_validasi_id', 3)
            ->orderByDesc('tanggal');

        if ($scopeKelasId) {
            $query->where('kelas_id', $scopeKelasId);
        } elseif ($request->filled('kelas_id')) {
            $query->where('kelas_id', $request->kelas_id);
        }
        if ($periodeId) {
            $query->where('periode_akademik_id', $periodeId);
        }
        if ($request->filled('dari')) {
            $query->whereDate('tanggal', '>=', $request->dari);
        }
        if ($request->filled('sampai')) {
            $query->whereDate('tanggal', '<=', $request->sampai);
        }

        $absensiList = $query->get();

        // Tabel bawah: sudah Disetujui (history)
        $historyQuery = Absensi::with(['kelas', 'periodeAkademik', 'statusValidasi', 'userUpdate', 'userInput'])
            ->where('status', 1)
            ->where('status_validasi_id', 5)
            ->orderByDesc('tanggal_update');

        if ($scopeKelasId) {
            $historyQuery->where('kelas_id', $scopeKelasId);
        } elseif ($request->filled('kelas_id')) {
            $historyQuery->where('kelas_id', $request->kelas_id);
        }
        if ($periodeId) {
            $historyQuery->where('periode_akademik_id', $periodeId);
        }
        if ($request->filled('dari')) {
            $historyQuery->whereDate('tanggal', '>=', $request->dari);
        }
        if ($request->filled('sampai')) {
            $historyQuery->whereDate('tanggal', '<=', $request->sampai);
        }

        $historyList = $historyQuery->get();

        $scopeKelas  = $scopeKelasId ? Kelas::find($scopeKelasId) : null;
        $kelasList   = $scopeKelasId
                     ? Kelas::where('id', $scopeKelasId)->get()
                     : Kelas::where('status', 1)->orderBy('nama_kelas')->get();
        $periodeList = PeriodeAkademik::where('status', 1)->orderByDesc('id')->get();

        return view('Absensi/index-walikelas', compact(
            'absensiList', 'historyList', 'scopeKelas', 'kelasList', 'periodeList', 'periodeId'
        ));
    }

    /**
     * Scope kelas untuk flow validasi: Wali Kelas → kelas-nya sendiri,
     * Admin/Petugas Piket → null (lihat semua), selain itu → 403.
     */
    private function resolveValidationScopeKelasId(): ?int
    {
        $user = auth()->user();
        if ($user->hasRole('Wali Kelas')) {
            $id = $user->waliKelasId();
            if (!$id) {
                abort(403, 'Akun Anda belum tertaut ke data guru/kelas. Hubungi Admin.');
            }
            return $id;
        }
        if ($user->hasRole(['Admin', 'Petugas Piket'])) {
            return null;
        }
        abort(403);
    }

    // ── WALI KELAS / PETUGAS PIKET: Validasi satu absensi ────────────────────

    public function waliKelasValidasi(Request $request, string $id)
    {
        $user         = auth()->user();
        $scopeKelasId = $this->resolveValidationScopeKelasId();

        $query = Absensi::where('status', 1)->where('status_validasi_id', 3);
        if ($scopeKelasId) {
            $query->where('kelas_id', $scopeKelasId);
        }

        $absensi = $query->findOrFail($id);

        $absensi->update([
            'status_validasi_id' => 5,
            'user_update'          => $user->id,
            'tanggal_update'       => now(),
        ]);

        return redirect()->route('Absensi.validasi.index')
            ->with('success', 'Absensi berhasil divalidasi.');
    }

    // ── WALI KELAS / PETUGAS PIKET: Bulk validasi ─────────────────────────────

    public function waliKelasBulkValidasi(Request $request)
    {
        $request->validate([
            'ids'         => ['required', 'array', 'min:1'],
            'ids.*'       => ['integer'],
            'bulk_action' => ['nullable', 'in:validasi,revisi'],
        ]);

        $user         = auth()->user();
        $scopeKelasId = $this->resolveValidationScopeKelasId();
        $action       = $request->input('bulk_action', 'validasi');

        // 5 = Disetujui, 6 = Perlu Revisi
        $targetStatusId = $action === 'revisi' ? 6 : 5;

        $query = Absensi::where('status', 1)
            ->where('status_validasi_id', 3)
            ->whereIn('id', $request->ids);

        if ($scopeKelasId) {
            $query->where('kelas_id', $scopeKelasId);
        }

        $updated = $query->update([
            'status_validasi_id' => $targetStatusId,
            'user_update'          => $user->id,
            'tanggal_update'       => now(),
        ]);

        $message = $action === 'revisi'
            ? $updated . ' absensi ditandai perlu revisi.'
            : $updated . ' absensi berhasil divalidasi.';

        return redirect()->route('Absensi.validasi.index')
            ->with('success', $message);
    }

    // ── WALI KELAS / PETUGAS PIKET: History validasi ──────────────────────────

    public function waliKelasHistory(Request $request)
    {
        $scopeKelasId = $this->resolveValidationScopeKelasId();

        $query = Absensi::with(['kelas', 'periodeAkademik', 'statusValidasi'])
            ->where('status', 1)
            ->whereIn('status_validasi_id', [4, 5, 6])
            ->orderByDesc('tanggal_update');

        if ($scopeKelasId) {
            $query->where('kelas_id', $scopeKelasId);
        }

        if ($request->filled('dari')) {
            $query->whereDate('tanggal', '>=', $request->dari);
        }
        if ($request->filled('sampai')) {
            $query->whereDate('tanggal', '<=', $request->sampai);
        }

        $historyList = $query->get();
        $kelas       = null;

        return view('Absensi/history-walikelas', compact('historyList', 'kelas'));
    }
}