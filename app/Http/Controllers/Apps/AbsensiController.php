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
        $kelas   = Kelas::where('status', 1)->orderBy('nama_kelas')->get();
        $periode = PeriodeAkademik::where('status', 1)->get();

        return view('Absensi.create', compact('kelas', 'periode'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kelas_id'            => ['required', 'integer'],
            'tanggal'             => ['required', 'date'],
            'periode_akademik_id' => ['required', 'integer'],
        ]);

        $statusBelumDiisi = StatusVerifikasi::where('nama_status', 'Menunggu Pengisian')
                                ->where('status', 1)->first();

        Absensi::create([
            'kelas_id'             => $request->kelas_id,
            'tanggal'              => $request->tanggal,
            'status_verifikasi_id' => $statusBelumDiisi?->id,
            'periode_akademik_id'  => $request->periode_akademik_id,
            'status'               => '1',
            'user_input'           => auth()->user()->id,
            'tanggal_input'        => date('Y-m-d H:i:s'),
        ]);

        return redirect()->route('Absensi.index')
            ->with('success', 'Data absensi berhasil ditambahkan.');
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

        // Siswa yang sudah tercatat (dari keterlambatan/dispensasi)
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

                // Simpan jam jika ada (per jam = is_full_day=0)
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

            // Update status → Menunggu Wali (id=3)
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
}