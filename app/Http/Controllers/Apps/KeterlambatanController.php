<?php

namespace App\Http\Controllers\Apps;

use App\Http\Controllers\Controller;
use App\Models\Admin\Siswa;
use App\Models\Apps\Kelas;
use App\Models\Apps\Absensi;
use App\Models\Apps\AbsensiDetail;
use App\Models\Apps\Keterlambatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;

class KeterlambatanController extends Controller
{
    public function index(Request $request)
    {
        $dari   = $request->dari ?? now()->subDays(7)->toDateString();
        $sampai = $request->sampai ?? now()->toDateString();

        $query = Keterlambatan::with(['siswa', 'absensi.kelas', 'periodeAkademik'])
            ->where('status', 1)
            ->orderByDesc('waktu_masuk');

        // Filter range tanggal (berdasarkan tanggal absensi)
        $query->whereHas('absensi', function ($q) use ($dari, $sampai) {
            $q->whereDate('tanggal', '>=', $dari)
            ->whereDate('tanggal', '<=', $sampai);
        });

        // Filter kelas
        if ($request->filled('kelas_id')) {
            $query->whereHas('absensi', function ($q) use ($request) {
                $q->where('kelas_id', $request->kelas_id);
            });
        }

        $keterlambatan = $query->paginate(25);
        $kelas         = Kelas::where('status', 1)->orderBy('nama_kelas')->get();

        return view('Keterlambatan.index', compact(
            'keterlambatan',
            'kelas',
            'dari',
            'sampai'
        ));
    }

    public function create(Request $request)
    {
        $kelas         = Kelas::where('status', 1)->orderBy('nama_kelas')->get();
        $absensi       = null;
        $siswa         = collect();
        $sudahTercatat = collect();

        if ($request->filled('kelas_id') && $request->filled('tanggal')) {
            // Cari absensi berdasarkan kelas_id + tanggal yang dipilih
            $absensi = Absensi::with(['kelas', 'periodeAkademik'])
                ->where('kelas_id', $request->kelas_id)
                ->where('status', 1)
                ->whereDate('tanggal', $request->tanggal)
                ->first();

            if ($absensi) {
                $sudahTerlambatIds = Keterlambatan::where('absensi_id', $absensi->id)
                    ->where('status', 1)
                    ->pluck('siswa_id');

                $siswa = Siswa::where('kelas_id', $request->kelas_id)
                    ->where('status', 1)
                    ->whereNotIn('id', $sudahTerlambatIds)
                    ->orderBy('nama_siswa')
                    ->get();

                $sudahTercatat = Keterlambatan::with('siswa')
                    ->where('absensi_id', $absensi->id)
                    ->where('status', 1)
                    ->orderBy('waktu_masuk')
                    ->get();
            }
        }

        return view('Keterlambatan.create', compact('kelas', 'absensi', 'siswa', 'sudahTercatat'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'absensi_id'  => ['required', 'integer'],
            'siswa_id'    => ['required', 'integer'],
            'waktu_masuk' => ['required', 'date_format:H:i'],
            'alasan'      => ['nullable', 'string', 'max:255'],
        ]);

        try {
            $absensi = Absensi::findOrFail($request->absensi_id);

            DB::transaction(function () use ($request, $absensi) {
                AbsensiDetail::updateOrCreate(
                    [
                        'absensi_id' => $absensi->id,
                        'siswa_id'   => $request->siswa_id,
                    ],
                    [
                        'status_absensi_id' => 5, // Terlambat
                        'is_full_day'       => 1,
                        'keterangan'        => $request->alasan,
                        'status'            => '1',
                        'user_input'        => auth()->user()->id,
                        'tanggal_input'     => date('Y-m-d H:i:s'),
                    ]
                );

                Keterlambatan::create([
                    'absensi_id'          => $absensi->id,
                    'siswa_id'            => $request->siswa_id,
                    'waktu_masuk'         => Carbon::parse($absensi->tanggal)->format('Y-m-d')
                                            . ' ' . $request->waktu_masuk . ':00',
                    'alasan'              => $request->alasan,
                    'periode_akademik_id' => $absensi->periode_akademik_id,
                    'status'              => '1',
                    'user_input'          => auth()->user()->id,
                    'tanggal_input'       => date('Y-m-d H:i:s'),
                ]);
            });

            return redirect()->route('Keterlambatan.create', [
                'kelas_id' => $request->kelas_id,
                'tanggal'  => $request->tanggal,
            ])->with('success', 'Keterlambatan berhasil dicatat.');

        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menyimpan: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy($id)
    {
        $kt = Keterlambatan::where('status', 1)->findOrFail($id);

        DB::transaction(function () use ($kt) {
            AbsensiDetail::where('absensi_id', $kt->absensi_id)
                ->where('siswa_id', $kt->siswa_id)
                ->where('status_absensi_id', 5)
                ->update([
                    'status_absensi_id' => 1,
                    'user_update'       => auth()->user()->id,
                    'tanggal_update'    => date('Y-m-d H:i:s'),
                ]);

            $kt->update([
                'status'         => 9,
                'user_update'    => auth()->user()->id,
                'tanggal_update' => date('Y-m-d H:i:s'),
            ]);
        });

        return redirect()->back()->with('success', 'Data keterlambatan dihapus.');
    }
}