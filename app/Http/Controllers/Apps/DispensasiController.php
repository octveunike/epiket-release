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
use App\Models\Reference\StatusVerifikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Exception;

class DispensasiController extends Controller
{
    public function index(Request $request)
    {
        // Default tanggal: H-7 sampai hari ini
        $dari   = $request->dari ?? now()->subDays(7)->toDateString();
        $sampai = $request->sampai ?? now()->toDateString();

        $query = Dispensasi::with(['organisasi', 'statusVerifikasi'])
            ->withCount('details')
            ->where('status', 1)
            ->orderByDesc('waktu_mulai');

        // Filter range tanggal
        $query->whereDate('waktu_mulai', '>=', $dari)
            ->whereDate('waktu_mulai', '<=', $sampai);

        // Filter organisasi (opsional)
        if ($request->filled('organisasi_id')) {
            $query->where('organisasi_id', $request->organisasi_id);
        }

        $dispensasi        = $query->get();
        $organisasi        = Organisasi::where('status', 1)->orderBy('nama_organisasi')->get();
        $statusDisetujuiId = StatusVerifikasi::where('nama_status', 'Disetujui')
                                    ->where('status', 1)
                                    ->value('id');

        return view('Dispensasi.index', compact(
            'dispensasi',
            'organisasi',
            'statusDisetujuiId',
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
            'kegiatan'            => ['required', 'string', 'max:255'],
            'lampiran_dispensasi' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
        ]);

        $lampiranPath = null;
        if ($request->hasFile('lampiran_dispensasi')) {
            $lampiranPath = $request->file('lampiran_dispensasi')
                ->store('dispensasi/lampiran', 'public');
        }

        $statusMenunggu = StatusVerifikasi::where('nama_status', 'Menunggu Piket')->where('status', 1)->first();
        $statusDisetujui = StatusVerifikasi::where('nama_status', 'Disetujui')->where('status', 1)->first();

        $user = auth()->user();
        $statusId = $user->hasRole('Petugas Piket')
            ? $statusDisetujui?->id   // auto approve
            : $statusMenunggu?->id;  // default

        $dispensasi = Dispensasi::create([
            'organisasi_id'        => $request->organisasi_id,
            'periode_akademik_id'  => $request->periode_akademik_id,
            'waktu_mulai'          => $request->waktu_mulai,
            'waktu_selesai'        => $request->waktu_selesai,
            'kegiatan'             => $request->kegiatan,
            'lampiran_dispensasi'  => $lampiranPath,
            'status_verifikasi_id' => $statusId,
            'status'               => '1',
            'user_input'           => auth()->user()->id,
            'tanggal_input'        => date('Y-m-d H:i:s'),
        ]);

        return redirect()->route('Dispensasi.show', $dispensasi->id)
            ->with('success', 'Dispensasi berhasil dibuat. Silakan tambahkan siswa.');
    }

    public function storeDetail(Request $request, string $id)
    {
        $request->validate([
            'siswa_id' => ['required', 'integer'],
        ]);

        $dispensasi = Dispensasi::where('status', 1)->findOrFail($id);

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

        return redirect()->back()->with('success', 'Siswa berhasil ditambahkan.');
    }

    public function verifikasi(string $id)
    {
        $dispensasi = Dispensasi::with('details')->where('status', 1)->findOrFail($id);

        $statusDisetujui = StatusVerifikasi::where('nama_status', 'Disetujui')->where('status', 1)->first();

        $siswaIds   = $dispensasi->details->pluck('siswa_id');
        $tglMulai   = Carbon::parse($dispensasi->waktu_mulai)->startOfDay();
        $tglSelesai = Carbon::parse($dispensasi->waktu_selesai)->endOfDay();

        if ($siswaIds->isEmpty()) {
            return redirect()->back()->with('error', 'Belum ada siswa yang ditambahkan ke dispensasi ini.');
        }

        DB::transaction(function () use ($dispensasi, $siswaIds, $tglMulai, $tglSelesai, $statusDisetujui) {

            $absensiList = Absensi::whereBetween('tanggal', [$tglMulai, $tglSelesai])
                ->where('status', 1)
                ->get();

            foreach ($absensiList as $absensi) {
                foreach ($siswaIds as $siswaId) {
                    AbsensiDetail::updateOrCreate(
                        [
                            'absensi_id' => $absensi->id,
                            'siswa_id'   => $siswaId,
                        ],
                        [
                            'status_absensi_id' => 4,
                            'is_full_day'       => 1,
                            'keterangan'        => 'Dispensasi: ' . $dispensasi->kegiatan,
                            'status'            => '1',
                            'user_input'        => auth()->user()->id,
                            'tanggal_input'     => date('Y-m-d H:i:s'),
                        ]
                    );
                }
            }

            $dispensasi->update([
                'status_verifikasi_id' => $statusDisetujui?->id,
                'user_update'          => auth()->user()->id,
                'tanggal_update'       => date('Y-m-d H:i:s'),
            ]);
        });

        return redirect()->back()->with('success', 'Dispensasi berhasil diverifikasi dan absensi siswa telah diperbarui.');
    }

    public function destroyDetail(string $id)
    {
        $detail = DispensasiDetail::where('status', 1)->findOrFail($id);
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
            'statusVerifikasi',
            'details.siswa.kelas',
        ])->where('status', 1)->findOrFail($id);

        $kelas = Kelas::where('status', 1)->orderBy('nama_kelas')->get();

        $sudahDitambahkan = $dispensasi->details->pluck('siswa_id');

        $siswaPerKelas = Siswa::where('status', 1)
            ->whereNotIn('id', $sudahDitambahkan)
            ->orderBy('nama_siswa')
            ->get(['id', 'nama_siswa', 'kelas_id'])
            ->groupBy('kelas_id')
            ->map(fn($group) => $group->values());

        $statusDisetujuiId = StatusVerifikasi::where('nama_status', 'Disetujui')->where('status', 1)->value('id');

        return view('Dispensasi.show', compact('dispensasi', 'kelas', 'siswaPerKelas', 'statusDisetujuiId'));
    }
}