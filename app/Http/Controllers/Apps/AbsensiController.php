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
use App\Models\Reference\StatusVerifikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;

class AbsensiController extends Controller
{
    public function index(Request $request)
    {
        // Wali Kelas tidak punya akses ke index umum, redirect ke halaman validasinya
        if (auth()->user()->hasRole('Wali Kelas')) {
            return redirect()->route('Absensi.walikelas.index');
        }

        $query = Absensi::withCount('details')
            ->with(['kelas', 'periodeAkademik', 'statusVerifikasi'])
            ->where('status', 1)
            ->orderByDesc('tanggal');

        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
        }

        if ($request->filled('kelas_id')) {
            $query->where('kelas_id', $request->kelas_id);
        }

        $absensiList               = $query->get();
        $kelas                     = Kelas::where('status', 1)->orderBy('nama_kelas')->get();
        $statusMenungguPengisianId = StatusVerifikasi::where('nama_status', 'Menunggu Pengisian')
                                        ->where('status', 1)->value('id');

        return view('Absensi.index', compact('absensiList', 'kelas', 'statusMenungguPengisianId'));
    }

    public function create()
    {
        $kelas       = Kelas::where('status', 1)->orderBy('nama_kelas')->get();
        $periodeAktif = PeriodeAkademik::where('status', 1)->first();

        return view('Absensi.create', compact('kelas', 'periodeAktif'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kelas_id' => ['required', 'integer'],
            'tanggal'  => ['required', 'date'],
        ]);

        $periodeAktif = PeriodeAkademik::where('status', 1)->first();

        if (!$periodeAktif) {
            return back()->with('error', 'Tidak ada periode akademik aktif.')->withInput();
        }

        $statusBelumDiisi = StatusVerifikasi::where('nama_status', 'Menunggu Pengisian')
                                ->where('status', 1)->first();

        $absensi = Absensi::create([
            'kelas_id'             => $request->kelas_id,
            'tanggal'              => $request->tanggal,
            'status_verifikasi_id' => $statusBelumDiisi?->id,
            'periode_akademik_id'  => $periodeAktif->id,
            'status'               => '1',
            'user_input'           => auth()->user()->id,
            'tanggal_input'        => date('Y-m-d H:i:s'),
        ]);

        return redirect()->route('Absensi.isiAbsensi', $absensi->id)
            ->with('success', 'Data absensi berhasil ditambahkan. Silakan isi absensi.');
    }

    public function isiAbsensi(string $id)
    {
        $absensi = Absensi::with(['kelas', 'periodeAkademik'])
            ->where('status', 1)
            ->findOrFail($id);

        $statusMenungguPengisianId = StatusVerifikasi::where('nama_status', 'Menunggu Pengisian')
                                        ->where('status', 1)->value('id');

        // Hanya block jika sudah disetujui final (id=5) atau ditolak (id=6)
        if (in_array($absensi->status_verifikasi_id, [5, 6])) {
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
                'status_verifikasi_id' => 3,
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
            'statusVerifikasi',
            'details.siswa',
            'details.statusAbsensi',
            'details.jams.jam',
        ])->where('status', 1)->findOrFail($id);

        return view('Absensi.show', compact('absensi'));
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

    // ── WALI KELAS: Index (daftar menunggu validasi) ──────────

    public function waliKelasIndex(Request $request)
    {
        $user = auth()->user();

        // Tabel atas: semua absensi menunggu validasi wali
        $query = Absensi::with(['kelas', 'periodeAkademik', 'statusVerifikasi'])
            ->where('status', 1)
            ->where('status_verifikasi_id', 3)
            ->orderByDesc('tanggal');

        if ($request->filled('dari')) {
            $query->whereDate('tanggal', '>=', $request->dari);
        }
        if ($request->filled('sampai')) {
            $query->whereDate('tanggal', '<=', $request->sampai);
        }

        $absensiList = $query->get();

        // Tabel bawah: sudah divalidasi (history)
        $historyQuery = Absensi::with(['kelas', 'periodeAkademik', 'statusVerifikasi'])
            ->where('status', 1)
            ->whereIn('status_verifikasi_id', [4, 5, 6])
            ->orderByDesc('tanggal_update');

        if ($request->filled('dari')) {
            $historyQuery->whereDate('tanggal', '>=', $request->dari);
        }
        if ($request->filled('sampai')) {
            $historyQuery->whereDate('tanggal', '<=', $request->sampai);
        }

        $historyList = $historyQuery->get();
        $kelas       = null;

        return view('Absensi/index-walikelas', compact('absensiList', 'historyList', 'kelas'));
    }

    // ── WALI KELAS: Validasi satu absensi ────────────────────

    public function waliKelasValidasi(Request $request, string $id)
    {
        $user    = auth()->user();
        $absensi = Absensi::where('status', 1)
            ->where('status_verifikasi_id', 3)
            ->findOrFail($id);

        $absensi->update([
            'status_verifikasi_id' => 5,
            'user_update'          => $user->id,
            'tanggal_update'       => now(),
        ]);

        return redirect()->route('Absensi.walikelas.index')
            ->with('success', 'Absensi berhasil divalidasi.');
    }

    // ── WALI KELAS: Bulk validasi ─────────────────────────────

    public function waliKelasBulkValidasi(Request $request)
    {
        $request->validate([
            'ids'   => ['required', 'array', 'min:1'],
            'ids.*' => ['integer'],
        ]);

        $user    = auth()->user();
        $updated = Absensi::where('status', 1)
            ->where('status_verifikasi_id', 3)
            ->whereIn('id', $request->ids)
            ->update([
                'status_verifikasi_id' => 5,
                'user_update'          => $user->id,
                'tanggal_update'       => now(),
            ]);

        return redirect()->route('Absensi.walikelas.index')
            ->with('success', $updated . ' absensi berhasil divalidasi.');
    }

    // ── WALI KELAS: History validasi ──────────────────────────

    public function waliKelasHistory(Request $request)
    {
        $query = Absensi::with(['kelas', 'periodeAkademik', 'statusVerifikasi'])
            ->where('status', 1)
            ->whereIn('status_verifikasi_id', [4, 5, 6])
            ->orderByDesc('tanggal_update');

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