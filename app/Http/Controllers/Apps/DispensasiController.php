<?php

namespace App\Http\Controllers\Apps;

use App\Http\Controllers\Controller;
use App\Models\Admin\Siswa;
use App\Models\Apps\Dispensasi;
use App\Models\Apps\DispensasiDetail;
use App\Models\Apps\Kelas;
use App\Models\Apps\Absensi;
use App\Models\Apps\AbsensiDetail;
use App\Models\Apps\PeriodeAkademik;
use App\Models\Apps\Organisasi;
use App\Models\Reference\StatusValidasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Exception;

class DispensasiController extends Controller
{
    public function index(Request $request)
    {
        $dari   = $request->dari   ?? now()->subDays(7)->toDateString();
        $sampai = $request->sampai ?? now()->addDays(30)->toDateString();

        $statusDisetujuiId         = StatusValidasi::where('nama_status', 'Disetujui')
                                        ->where('status', 1)
                                        ->value('id');
        $statusMenungguPengisianId = StatusValidasi::where('nama_status', 'Menunggu Pengisian')
                                        ->where('status', 1)
                                        ->value('id');
        $statusMenungguPiketId     = StatusValidasi::where('nama_status', 'Menunggu Piket')
                                        ->where('status', 1)
                                        ->value('id');
        $statusPerluRevisiId       = StatusValidasi::where('nama_status', 'Perlu Revisi')
                                        ->where('status', 1)
                                        ->value('id');

        // Default Periode Akademik = periode aktif (status = 1)
        $periodeAktif = PeriodeAkademik::where('status', 1)->first();
        $periodeId    = $request->filled('periode_akademik_id')
                      ? (int) $request->periode_akademik_id
                      : ($periodeAktif?->id);

        // Tabel atas: semua dispensasi yang belum Disetujui
        $query = Dispensasi::with(['organisasi', 'statusValidasi', 'userUpdate', 'userInput'])
            ->withCount('details')
            ->where('status', 1)
            ->where('status_validasi_id', '!=', $statusDisetujuiId)
            // Menunggu Piket selalu di paling atas, lalu sort by tanggal terbaru
            ->orderByRaw('status_validasi_id = ? DESC', [$statusMenungguPiketId])
            ->orderByDesc('waktu_mulai')
            // Overlap filter: tampilkan dispensasi yang periode kegiatannya
            // [waktu_mulai, waktu_selesai] beririsan dengan [dari, sampai].
            ->whereDate('waktu_mulai', '<=', $sampai)
            ->whereDate('waktu_selesai', '>=', $dari);

        if ($request->filled('organisasi_id')) {
            $query->where('organisasi_id', $request->organisasi_id);
        }
        if ($periodeId) {
            $query->where('periode_akademik_id', $periodeId);
        }

        $dispensasi = $query->get();

        // Tabel bawah: history (Disetujui)
        $historyQuery = Dispensasi::with(['organisasi', 'statusValidasi', 'userUpdate', 'userInput'])
            ->withCount('details')
            ->where('status', 1)
            ->where('status_validasi_id', $statusDisetujuiId)
            ->orderByDesc('waktu_mulai')
            ->whereDate('waktu_mulai', '<=', $sampai)
            ->whereDate('waktu_selesai', '>=', $dari);

        if ($request->filled('organisasi_id')) {
            $historyQuery->where('organisasi_id', $request->organisasi_id);
        }
        if ($periodeId) {
            $historyQuery->where('periode_akademik_id', $periodeId);
        }

        $historyList = $historyQuery->get();

        $organisasi  = Organisasi::where('status', 1)->orderBy('nama_organisasi')->get();
        $periodeList = PeriodeAkademik::where('status', 1)->orderByDesc('id')->get();

        return view('Dispensasi.index', compact(
            'dispensasi',
            'historyList',
            'organisasi',
            'periodeList',
            'periodeId',
            'statusDisetujuiId',
            'statusMenungguPengisianId',
            'statusMenungguPiketId',
            'statusPerluRevisiId',
            'dari',
            'sampai'
        ));
    }

    public function create()
    {
        $organisasi = Organisasi::where('status', 1)->orderBy('nama_organisasi')->get();
        $periode    = PeriodeAkademik::where('status', 1)->get();

        return view('Dispensasi.create', compact('organisasi', 'periode'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'organisasi_id'       => ['nullable', 'integer'],
            'periode_akademik_id' => ['required', 'integer'],
            'waktu_mulai'         => ['required', 'date'],
            'waktu_selesai'       => ['required', 'date', 'after_or_equal:waktu_mulai'],
            'nama_kegiatan'       => ['required', 'string', 'max:255'],
            'lampiran_dispensasi' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
        ]);

        $lampiranPath = null;
        if ($request->hasFile('lampiran_dispensasi')) {
            $lampiranPath = $request->file('lampiran_dispensasi')
                ->store('dispensasi/lampiran', 'public');
        }

        $statusAwalId = StatusValidasi::where('nama_status', 'Menunggu Pengisian')
                            ->where('status', 1)->value('id');

        $dispensasi = Dispensasi::create([
            'organisasi_id'        => $request->organisasi_id,
            'periode_akademik_id'  => $request->periode_akademik_id,
            'waktu_mulai'          => $request->waktu_mulai,
            'waktu_selesai'        => $request->waktu_selesai,
            'nama_kegiatan'        => $request->nama_kegiatan,
            'lampiran_dispensasi'  => $lampiranPath,
            'status_validasi_id' => $statusAwalId,
            'status'               => '1',
            'user_input'           => auth()->user()->id,
            'tanggal_input'        => date('Y-m-d H:i:s'),
        ]);

        return redirect()->route('Dispensasi.show', $dispensasi->id)
            ->with('success', 'Dispensasi berhasil dibuat. Silakan tambahkan data siswa yang dispen.');
    }

    public function edit(string $id)
    {
        $dispensasi = Dispensasi::where('status', 1)->findOrFail($id);
        $this->authorizeDetailEdit($dispensasi);

        $organisasi = Organisasi::where('status', 1)->orderBy('nama_organisasi')->get();
        $periode    = PeriodeAkademik::where('status', 1)->get();

        return view('Dispensasi.edit', compact('dispensasi', 'organisasi', 'periode'));
    }

    public function update(Request $request, string $id)
    {
        $dispensasi = Dispensasi::where('status', 1)->findOrFail($id);
        $this->authorizeDetailEdit($dispensasi);

        $request->validate([
            'organisasi_id'       => ['nullable', 'integer'],
            'periode_akademik_id' => ['required', 'integer'],
            'waktu_mulai'         => ['required', 'date'],
            'waktu_selesai'       => ['required', 'date', 'after_or_equal:waktu_mulai'],
            'nama_kegiatan'       => ['required', 'string', 'max:255'],
            'lampiran_dispensasi' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
        ]);

        $lampiranPath = $dispensasi->lampiran_dispensasi;
        if ($request->hasFile('lampiran_dispensasi')) {
            if ($lampiranPath) {
                Storage::disk('public')->delete($lampiranPath);
            }
            $lampiranPath = $request->file('lampiran_dispensasi')
                ->store('dispensasi/lampiran', 'public');
        }

        $dispensasi->update([
            'organisasi_id'       => $request->organisasi_id,
            'periode_akademik_id' => $request->periode_akademik_id,
            'waktu_mulai'         => $request->waktu_mulai,
            'waktu_selesai'       => $request->waktu_selesai,
            'nama_kegiatan'       => $request->nama_kegiatan,
            'lampiran_dispensasi' => $lampiranPath,
            'user_update'         => auth()->user()->id,
            'tanggal_update'      => date('Y-m-d H:i:s'),
        ]);

        return redirect()->route('Dispensasi.show', $dispensasi->id)
            ->with('success', 'Dispensasi berhasil diperbarui.');
    }

    public function storeDetail(Request $request, string $id)
    {
        $request->validate([
            'siswa_id' => ['required', 'integer'],
        ]);

        $dispensasi = Dispensasi::where('status', 1)->findOrFail($id);
        $this->authorizeDetailEdit($dispensasi);

        $user = auth()->user();
        if ($user->hasRole('Ketua Kelas')) {
            $ketuaKelasId = $user->ketuaKelasId();
            $siswaKelasId = Siswa::where('id', $request->siswa_id)->value('kelas_id');
            if (!$ketuaKelasId || (int) $siswaKelasId !== (int) $ketuaKelasId) {
                return redirect()->back()->with('error', 'Anda hanya dapat menambahkan siswa dari kelas Anda sendiri.');
            }
        }

        $sudahAda = DispensasiDetail::where('dispensasi_id', $dispensasi->id)
            ->where('siswa_id', $request->siswa_id)
            ->where('status', 1)
            ->exists();

        if ($sudahAda) {
            return redirect()->back()->with('error', 'Siswa ini sudah ada dalam daftar dispensasi.');
        }

        DispensasiDetail::create([
            'dispensasi_id' => $dispensasi->id,
            'siswa_id'      => $request->siswa_id,
            'status'        => '1',
            'user_input'    => auth()->user()->id,
            'tanggal_input' => date('Y-m-d H:i:s'),
        ]);

        $statusDisetujuiId = StatusValidasi::where('nama_status', 'Disetujui')->where('status', 1)->value('id');

        if ((int) $dispensasi->status_validasi_id === (int) $statusDisetujuiId) {
            $this->propagateDispensasiToAbsensi($dispensasi, [(int) $request->siswa_id]);
        }

        return redirect()->back()->with('success', 'Siswa berhasil ditambahkan.');
    }

    public function ajukan(string $id)
    {
        $dispensasi = Dispensasi::with('details')->where('status', 1)->findOrFail($id);
        $this->authorizeDetailEdit($dispensasi);

        $editableIds = StatusValidasi::whereIn('nama_status', ['Menunggu Pengisian', 'Perlu Revisi'])
            ->where('status', 1)->pluck('id')->map(fn ($v) => (int) $v)->all();

        if (!in_array((int) $dispensasi->status_validasi_id, $editableIds, true)) {
            return redirect()->back()->with('error', 'Dispensasi ini sudah diajukan sebelumnya.');
        }

        if ($dispensasi->details->isEmpty()) {
            return redirect()->back()->with('error', 'Tambahkan minimal satu siswa sebelum mengajukan dispensasi.');
        }

        $user = auth()->user();

        if ($user->hasRole(['Admin', 'Petugas Piket'])) {
            $statusDisetujuiId = StatusValidasi::where('nama_status', 'Disetujui')
                                    ->where('status', 1)->value('id');
            $siswaIds = $dispensasi->details->pluck('siswa_id')->map(fn ($v) => (int) $v)->all();

            DB::transaction(function () use ($dispensasi, $siswaIds, $statusDisetujuiId, $user) {
                $this->propagateDispensasiToAbsensi($dispensasi, $siswaIds);

                $dispensasi->update([
                    'status_validasi_id' => $statusDisetujuiId,
                    'user_update'          => $user->id,
                    'tanggal_update'       => date('Y-m-d H:i:s'),
                ]);
            });

            return redirect()->back()->with('success', 'Dispensasi berhasil diajukan. Absensi siswa telah diperbarui.');
        }

        $statusMenungguPiketId = StatusValidasi::where('nama_status', 'Menunggu Piket')
                                    ->where('status', 1)->value('id');

        $dispensasi->update([
            'status_validasi_id' => $statusMenungguPiketId,
            'user_update'          => $user->id,
            'tanggal_update'       => date('Y-m-d H:i:s'),
        ]);

        return redirect()->back()->with('success', 'Dispensasi berhasil diajukan dan menunggu validasi Petugas Piket.');
    }

    public function revisi(string $id)
    {
        if (!auth()->user()->hasRole(['Admin', 'Petugas Piket'])) {
            abort(403, 'Hanya Admin / Petugas Piket yang dapat mengajukan revisi.');
        }

        $dispensasi = Dispensasi::where('status', 1)->findOrFail($id);

        $statusDisetujuiId = StatusValidasi::where('nama_status', 'Disetujui')
                                ->where('status', 1)->value('id');

        if ((int) $dispensasi->status_validasi_id === (int) $statusDisetujuiId) {
            return redirect()->back()->with('error', 'Dispensasi yang sudah disetujui tidak dapat dikembalikan ke revisi.');
        }

        $statusPerluRevisiId = StatusValidasi::where('nama_status', 'Perlu Revisi')
                                ->where('status', 1)->value('id');

        $dispensasi->update([
            'status_validasi_id' => $statusPerluRevisiId,
            'user_update'          => auth()->user()->id,
            'tanggal_update'       => date('Y-m-d H:i:s'),
        ]);

        return redirect()->back()->with('success', 'Dispensasi dikembalikan untuk revisi.');
    }

    public function verifikasi(string $id)
    {
        $dispensasi = Dispensasi::with('details')->where('status', 1)->findOrFail($id);

        $statusDisetujui = StatusValidasi::where('nama_status', 'Disetujui')->where('status', 1)->first();

        $siswaIds = $dispensasi->details->pluck('siswa_id')->map(fn($v) => (int) $v)->all();

        if (empty($siswaIds)) {
            return redirect()->back()->with('error', 'Belum ada siswa yang ditambahkan ke dispensasi ini.');
        }

        DB::transaction(function () use ($dispensasi, $siswaIds, $statusDisetujui) {
            $this->propagateDispensasiToAbsensi($dispensasi, $siswaIds);

            $dispensasi->update([
                'status_validasi_id' => $statusDisetujui?->id,
                'user_update'          => auth()->user()->id,
                'tanggal_update'       => date('Y-m-d H:i:s'),
            ]);
        });

        return redirect()->back()->with('success', 'Dispensasi berhasil divalidasi dan absensi siswa telah diperbarui.');
    }

    private function propagateDispensasiToAbsensi(Dispensasi $dispensasi, array $siswaIds): void
    {
        if (empty($siswaIds)) {
            return;
        }

        $tglMulai   = Carbon::parse($dispensasi->waktu_mulai)->startOfDay();
        $tglSelesai = Carbon::parse($dispensasi->waktu_selesai)->endOfDay();

        $siswaKelasMap = Siswa::whereIn('id', $siswaIds)->pluck('kelas_id', 'id');

        foreach ($siswaKelasMap as $siswaId => $kelasId) {
            if (!$kelasId) {
                continue;
            }

            $absensiList = Absensi::whereBetween('tanggal', [$tglMulai, $tglSelesai])
                ->where('status', 1)
                ->where('kelas_id', $kelasId)
                ->get();

            foreach ($absensiList as $absensi) {
                AbsensiDetail::updateOrCreate(
                    [
                        'absensi_id' => $absensi->id,
                        'siswa_id'   => $siswaId,
                    ],
                    [
                        'status_absensi_id' => 4,
                        'is_full_day'       => 1,
                        'keterangan'        => 'Dispensasi: ' . $dispensasi->nama_kegiatan,
                        'status'            => '1',
                        'user_input'        => auth()->user()->id,
                        'tanggal_input'     => date('Y-m-d H:i:s'),
                    ]
                );
            }
        }
    }

    public function destroyDetail(string $id)
    {
        $detail = DispensasiDetail::with('dispensasi')->where('status', 1)->findOrFail($id);
        $this->authorizeDetailEdit($detail->dispensasi);

        $detail->update([
            'status'         => 9,
            'user_update'    => auth()->user()->id,
            'tanggal_update' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->back()->with('success', 'Siswa berhasil dihapus dari dispensasi.');
    }

    public function destroy(string $id)
    {
        $dispensasi = Dispensasi::where('status', 1)->findOrFail($id);
        $this->authorizeDestroy($dispensasi);

        DB::transaction(function () use ($dispensasi) {
            $dispensasi->details()->where('status', 1)->update([
                'status'         => 9,
                'user_update'    => auth()->user()->id,
                'tanggal_update' => date('Y-m-d H:i:s'),
            ]);

            if ($dispensasi->lampiran_dispensasi) {
                Storage::disk('public')->delete($dispensasi->lampiran_dispensasi);
            }

            $dispensasi->update([
                'status'         => 9,
                'user_update'    => auth()->user()->id,
                'tanggal_update' => date('Y-m-d H:i:s'),
            ]);
        });

        return redirect()->route('Dispensasi.index')
            ->with('success', 'Dispensasi berhasil dihapus.');
    }

    public function show(string $id)
    {
        $dispensasi = Dispensasi::with([
            'organisasi',
            'statusValidasi',
            'details.siswa.kelas',
        ])->where('status', 1)->findOrFail($id);

        $user         = auth()->user();
        $ketuaKelas   = $user->hasRole('Ketua Kelas') ? $user->ketuaKelas() : null;
        $ketuaKelasId = $ketuaKelas->id ?? null;

        $statusDisetujuiId         = StatusValidasi::where('nama_status', 'Disetujui')->where('status', 1)->value('id');
        $statusMenungguPengisianId = StatusValidasi::where('nama_status', 'Menunggu Pengisian')->where('status', 1)->value('id');
        $statusPerluRevisiId       = StatusValidasi::where('nama_status', 'Perlu Revisi')->where('status', 1)->value('id');

        // Admin/Petugas Piket: boleh kelola siswa kapan saja (late-add propagasi otomatis di storeDetail).
        // Ketua Kelas: hanya pada dispensasi miliknya sendiri dan hanya saat status Menunggu Pengisian
        // atau Perlu Revisi (setelah ditolak Piket, mereka boleh edit lagi).
        $canEditDetail = $user->hasRole(['Admin', 'Petugas Piket'])
            || ($user->hasRole('Ketua Kelas')
                && (int) $dispensasi->user_input === (int) $user->id
                && in_array((int) $dispensasi->status_validasi_id, [(int) $statusMenungguPengisianId, (int) $statusPerluRevisiId], true));

        $kelas = $ketuaKelasId
            ? Kelas::where('status', 1)->where('id', $ketuaKelasId)->get()
            : Kelas::where('status', 1)->orderBy('nama_kelas')->get();

        $sudahDitambahkan = $dispensasi->details->pluck('siswa_id');

        $siswaQuery = Siswa::where('status', 1)
            ->whereNotIn('id', $sudahDitambahkan)
            ->orderBy('nama_siswa');

        if ($ketuaKelasId) {
            $siswaQuery->where('kelas_id', $ketuaKelasId);
        }

        $siswaPerKelas = $siswaQuery->get(['id', 'nama_siswa', 'kelas_id'])
            ->groupBy('kelas_id')
            ->map(fn($group) => $group->values());

        return view('Dispensasi.show', compact(
            'dispensasi', 'kelas', 'siswaPerKelas',
            'statusDisetujuiId', 'statusMenungguPengisianId', 'statusPerluRevisiId',
            'canEditDetail', 'ketuaKelas'
        ));
    }

    private function authorizeDestroy(Dispensasi $dispensasi): void
    {
        $user = auth()->user();
        if ($user->hasRole(['Admin', 'Petugas Piket'])) {
            return;
        }
        if ($user->hasRole('Ketua Kelas') && (int) $dispensasi->user_input === (int) $user->id) {
            $editableIds = StatusValidasi::whereIn('nama_status', ['Menunggu Pengisian', 'Perlu Revisi'])
                ->where('status', 1)->pluck('id')->map(fn ($v) => (int) $v)->all();
            if (in_array((int) $dispensasi->status_validasi_id, $editableIds, true)) {
                return;
            }
            abort(403, 'Dispensasi ini sudah diajukan dan tidak dapat dihapus.');
        }
        abort(403, 'Anda tidak berhak menghapus dispensasi ini.');
    }

    private function authorizeDetailEdit(Dispensasi $dispensasi): void
    {
        $user = auth()->user();
        if ($user->hasRole(['Admin', 'Petugas Piket'])) {
            return;
        }
        if ($user->hasRole('Ketua Kelas') && (int) $dispensasi->user_input === (int) $user->id) {
            $editableIds = StatusValidasi::whereIn('nama_status', ['Menunggu Pengisian', 'Perlu Revisi'])
                ->where('status', 1)->pluck('id')->map(fn ($v) => (int) $v)->all();
            if (in_array((int) $dispensasi->status_validasi_id, $editableIds, true)) {
                return;
            }
            abort(403, 'Dispensasi ini sudah diajukan dan tidak dapat diubah.');
        }
        abort(403, 'Anda tidak berhak mengubah data siswa pada dispensasi ini.');
    }
}