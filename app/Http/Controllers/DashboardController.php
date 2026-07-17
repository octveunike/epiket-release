<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        // Panel dashboard yang benar-benar bisa dipakai user (urut prioritas).
        // Sumber tunggal ada di User::dashboardPanels().
        $panels = $user->dashboardPanels();

        if (empty($panels)) {
            return redirect()->route('login')->with('error', 'Akun Anda belum memiliki role. Hubungi Admin.');
        }

        // Panel aktif dari ?panel=, hanya jika user memang punya role tsb.
        // Kalau tidak valid, pakai panel prioritas tertinggi.
        $activePanel = $request->get('panel');
        if (!is_string($activePanel) || !isset($panels[$activePanel])) {
            $activePanel = array_key_first($panels);
        }

        // Bagikan ke semua view dashboard untuk merender panel switcher.
        view()->share('panels', $panels);
        view()->share('activePanel', $activePanel);

        return match ($activePanel) {
            'admin' => $this->dashboardAdmin($request),
            'piket' => $this->dashboardPetugasPiket($request),
            'wali'  => $this->dashboardWaliKelas($request),
            'ketua' => $this->dashboardKetuaKelas($request),
        };
    }

    // =========================================================
    // ADMIN
    // =========================================================
    private function dashboardAdmin(Request $request)
    {
        $user  = auth()->user();
        $today = Carbon::today();

        $periodeAktif = DB::table('periode_akademik')
            ->where('status', 1)->orderByDesc('id')->first();

        $totalSiswa      = DB::table('siswa')->where('status', 1)->count();
        $totalKelas      = DB::table('kelas')->where('status', 1)->count();
        $totalGuru       = DB::table('guru')->where('status', 1)->count();
        $totalOrganisasi = DB::table('organisasi')->where('status', 1)->count();

        $kelasHariIni = DB::table('absensi')
            ->whereDate('tanggal', $today)->where('status', 1)
            ->where('status_validasi_id', '>=', 3)
            ->distinct('kelas_id')->count('kelas_id');

        $kelasBelumIsi   = $totalKelas - $kelasHariIni;

        $kelasBelumAbsen = DB::table('kelas as k')
            ->leftJoin('absensi as a', function ($j) use ($today) {
                $j->on('a.kelas_id', '=', 'k.id')
                  ->whereDate('a.tanggal', $today)->where('a.status', 1)
                  ->where('a.status_validasi_id', '>=', 3);
            })
            ->where('k.status', 1)->whereNull('a.id')
            ->select('k.id', 'k.nama_kelas')->get();

        $rekap = DB::table('absensi_detail as ad')
            ->join('absensi as a', 'a.id', '=', 'ad.absensi_id')
            ->whereDate('a.tanggal', $today)
            ->where('ad.status', 1)->where('ad.is_full_day', 1)
            ->select('ad.status_absensi_id', DB::raw('COUNT(*) as total'))
            ->groupBy('ad.status_absensi_id')
            ->pluck('total', 'status_absensi_id');

        $totalIzin   = $rekap->get(1, 0);
        $totalSakit  = $rekap->get(2, 0);
        $totalAlpha  = $rekap->get(3, 0);
        $totalDispen = $rekap->get(4, 0);

        $totalTerlambat = DB::table('keterlambatan')
            ->whereDate('waktu_masuk', $today)->where('status', 1)->count();

        // Dispensasi pending: status 1 (Menunggu Pengisian) atau 6 (Perlu Revisi)
        $dispensasiPending   = DB::table('dispensasi')
            ->whereIn('status_validasi_id', [1, 6])->where('status', 1)->count();

        $absensiMenungguWali = DB::table('absensi')
            ->where('status_validasi_id', 3)->where('status', 1)->count();

        $totalTamuHariIni = DB::table('daftar_tamu')
            ->whereDate('tanggal_kunjungan', $today)->where('status', 1)
            ->count();

        $daftarTamu = DB::table('daftar_tamu')
            ->where('status', 1)
            ->orderByDesc('tanggal_kunjungan')
            ->orderByDesc('id')
            ->limit(5)->get();

        $keterlambatanTerbaru = DB::table('keterlambatan as kt')
            ->join('siswa as s', 's.id', '=', 'kt.siswa_id')
            ->join('kelas as kl', 'kl.id', '=', 's.kelas_id')
            ->whereDate('kt.waktu_masuk', $today)->where('kt.status', 1)
            ->select('kt.*', 's.nama_siswa', 'kl.nama_kelas')
            ->orderByDesc('kt.waktu_masuk')->limit(5)->get();



        $dispensasiTerbaru = DB::table('dispensasi as d')
            ->leftJoin('organisasi as o', 'o.id', '=', 'd.organisasi_id')
            ->join('status_validasi as sv', 'sv.id', '=', 'd.status_validasi_id')
            ->where('d.status', 1)
            ->select('d.*', 'o.nama_organisasi', 'sv.nama_status as nama_verifikasi')
            ->orderByDesc('d.id')->limit(5)->get();

        $daftarKelas = DB::table('kelas')->where('status', 1)->orderBy('nama_kelas')->get();

        return view('dashboard.admin', compact(
            'user', 'periodeAktif',
            'totalSiswa', 'totalKelas', 'totalGuru', 'totalOrganisasi',
            'kelasHariIni', 'kelasBelumIsi', 'kelasBelumAbsen',
            'totalIzin', 'totalSakit', 'totalAlpha', 'totalDispen',
            'totalTerlambat', 'dispensasiPending', 'absensiMenungguWali',
            'totalTamuHariIni', 'daftarTamu', 'keterlambatanTerbaru',
            'dispensasiTerbaru',
            'daftarKelas'
        ));
    }

    // =========================================================
    // PETUGAS PIKET
    // =========================================================
    private function dashboardPetugasPiket(Request $request)
    {
        $user  = auth()->user();
        $today = Carbon::today();

        $periodeAktif = DB::table('periode_akademik')
            ->where('status', 1)->orderByDesc('id')->first();

        $totalKelas     = DB::table('kelas')->where('status', 1)->count();
        $kelasUdahAbsen = DB::table('absensi')
            ->whereDate('tanggal', $today)->where('status', 1)
            ->where('status_validasi_id', '>=', 3)
            ->distinct('kelas_id')->count('kelas_id');

        $kelasBelumAbsen = DB::table('kelas as k')
            ->leftJoin('absensi as a', function ($j) use ($today) {
                $j->on('a.kelas_id', '=', 'k.id')
                  ->whereDate('a.tanggal', $today)->where('a.status', 1)
                  ->where('a.status_validasi_id', '>=', 3);
            })
            ->where('k.status', 1)->whereNull('a.id')
            ->select('k.id', 'k.nama_kelas')->get();

        $rekap = DB::table('absensi_detail as ad')
            ->join('absensi as a', 'a.id', '=', 'ad.absensi_id')
            ->whereDate('a.tanggal', $today)
            ->where('ad.status', 1)->where('ad.is_full_day', 1)
            ->select('ad.status_absensi_id', DB::raw('COUNT(*) as total'))
            ->groupBy('ad.status_absensi_id')
            ->pluck('total', 'status_absensi_id');

        $totalIzin   = $rekap->get(1, 0);
        $totalSakit  = $rekap->get(2, 0);
        $totalAlpha  = $rekap->get(3, 0);
        $totalDispen = $rekap->get(4, 0);

        $keterlambatanHariIni = DB::table('keterlambatan as kt')
            ->join('siswa as s', 's.id', '=', 'kt.siswa_id')
            ->join('kelas as kl', 'kl.id', '=', 's.kelas_id')
            ->whereDate('kt.waktu_masuk', $today)->where('kt.status', 1)
            ->select('kt.*', 's.nama_siswa', 'kl.nama_kelas')
            ->orderByDesc('kt.waktu_masuk')->get();

        $totalTerlambat = $keterlambatanHariIni->count();

        $dispensasiMenunggu = DB::table('dispensasi as d')
            ->leftJoin('organisasi as o', 'o.id', '=', 'd.organisasi_id')
            ->join('status_validasi as sv', 'sv.id', '=', 'd.status_validasi_id')
            ->where('d.status_validasi_id', 2)->where('d.status', 1)
            ->select('d.*', 'o.nama_organisasi', 'sv.nama_status as nama_verifikasi')
            ->orderByDesc('d.id')->get();

        $daftarTamu = DB::table('daftar_tamu')
            ->where('status', 1)
            ->orderByDesc('tanggal_kunjungan')
            ->orderByDesc('id')
            ->limit(5)->get();

        return view('dashboard.petugas_piket', compact(
            'user', 'periodeAktif',
            'totalKelas', 'kelasUdahAbsen', 'kelasBelumAbsen',
            'totalIzin', 'totalSakit', 'totalAlpha', 'totalDispen',
            'keterlambatanHariIni', 'totalTerlambat',
            'dispensasiMenunggu', 'daftarTamu'
        ));
    }

    // =========================================================
    // WALI KELAS
    // =========================================================
    private function dashboardWaliKelas(Request $request)
    {
        $user  = auth()->user();
        $today = Carbon::today();

        $guru  = DB::table('guru')->where('user_id', $user->id)->where('status', 1)->first();
        $kelas = $guru
            ? DB::table('kelas')->where('wali_kelas_id', $guru->id)->where('status', 1)->first()
            : null;

        $daftarKelas = DB::table('kelas')->where('status', 1)->orderBy('nama_kelas')->get();

        if (!$kelas) {
            return view('dashboard.wali_kelas', compact('user', 'kelas', 'daftarKelas'));
        }

        $periodeAktif = DB::table('periode_akademik')
            ->where('status', 1)->orderByDesc('id')->first();

        $totalSiswa = DB::table('siswa')
            ->where('kelas_id', $kelas->id)->where('status', 1)->count();

        $absensiHariIni = DB::table('absensi')
            ->where('kelas_id', $kelas->id)
            ->whereDate('tanggal', $today)->where('status', 1)->first();

        $rekap = collect();
        if ($absensiHariIni) {
            $rekap = DB::table('absensi_detail')
                ->where('absensi_id', $absensiHariIni->id)
                ->where('status', 1)->where('is_full_day', 1)
                ->select('status_absensi_id', DB::raw('COUNT(*) as total'))
                ->groupBy('status_absensi_id')
                ->pluck('total', 'status_absensi_id');
        }

        $totalHadir  = $totalSiswa - $rekap->sum();
        $totalIzin   = $rekap->get(1, 0);
        $totalSakit  = $rekap->get(2, 0);
        $totalAlpha  = $rekap->get(3, 0);
        $totalDispen = $rekap->get(4, 0);

        // Semua absensi kelas ini yang menunggu validasi wali
        $absensiMenungguValidasi = DB::table('absensi as a')
            ->join('status_validasi as sv', 'sv.id', '=', 'a.status_validasi_id')
            ->where('a.kelas_id', $kelas->id)
            ->where('a.status_validasi_id', 3)
            ->where('a.status', 1)
            ->select('a.*', 'sv.nama_status as nama_verifikasi')
            ->orderByDesc('a.tanggal')->get();

        $keterlambatanQuery = DB::table('keterlambatan as kt')
            ->join('siswa as s', 's.id', '=', 'kt.siswa_id')
            ->where('s.kelas_id', $kelas->id)
            ->where('kt.status', 1)
            ->select('kt.id', 's.nama_siswa', 'kt.waktu_masuk as waktu', 'kt.alasan as keterangan', DB::raw("'Terlambat' as jenis"))
            ->orderByDesc('kt.waktu_masuk')->limit(10)->get();

        $absensiDesc = DB::table('absensi_detail as ad')
            ->join('absensi as a', 'a.id', '=', 'ad.absensi_id')
            ->join('siswa as s', 's.id', '=', 'ad.siswa_id')
            ->join('status_absensi as sa', 'sa.id', '=', 'ad.status_absensi_id')
            ->where('s.kelas_id', $kelas->id)
            ->whereIn('ad.status_absensi_id', [1, 2, 3, 4])
            ->where('ad.status', 1)
            ->where('ad.is_full_day', 1)
            ->where('a.status', 1)
            ->select('ad.id', 's.nama_siswa', 'a.tanggal as waktu', 'ad.keterangan as keterangan', 'sa.keterangan as jenis')
            ->orderByDesc('a.tanggal')->limit(10)->get();

        $laporanAll = $keterlambatanQuery->concat($absensiDesc)->sortByDesc('waktu')->take(3)->values();

        $dispensasiAktif = DB::table('dispensasi as d')
            ->join('dispensasi_detail as dd', 'dd.dispensasi_id', '=', 'd.id')
            ->join('siswa as s', 's.id', '=', 'dd.siswa_id')
            ->where('s.kelas_id', $kelas->id)
            ->where('d.status', 1)->where('dd.status', 1)
            ->whereDate('d.waktu_selesai', '>=', $today)
            ->select('d.*')->distinct()->get();

        return view('dashboard.wali_kelas', compact(
            'user', 'kelas', 'daftarKelas', 'periodeAktif',
            'totalSiswa', 'totalHadir', 'totalIzin', 'totalSakit', 'totalAlpha', 'totalDispen',
            'absensiHariIni', 'absensiMenungguValidasi',
            'laporanAll',
            'dispensasiAktif'
        ));
    }

    // =========================================================
    // KETUA KELAS
    // =========================================================
    private function dashboardKetuaKelas(Request $request)
    {
        $user  = auth()->user();
        $today = Carbon::today();

        $kelas = DB::table('kelas')
            ->join('siswa as s', 's.id', '=', 'kelas.ketua_kelas_id')
            ->where('kelas.status', 1)->where('s.status', 1)
            ->where('s.user_id', $user->id)
            ->select('kelas.*')->first();

        $daftarKelas = DB::table('kelas')->where('status', 1)->orderBy('nama_kelas')->get();

        if (!$kelas) {
            return view('dashboard.ketua_kelas', compact('user', 'kelas', 'daftarKelas'));
        }

        $periodeAktif = DB::table('periode_akademik')
            ->where('status', 1)->orderByDesc('id')->first();

        $totalSiswa = DB::table('siswa')
            ->where('kelas_id', $kelas->id)->where('status', 1)->count();

        $absensiHariIni = DB::table('absensi')
            ->where('kelas_id', $kelas->id)
            ->whereDate('tanggal', $today)->where('status', 1)->first();

        $statusVerifHariIni = $absensiHariIni
            ? DB::table('status_validasi')->where('id', $absensiHariIni->status_validasi_id)->first()
            : null;

        $rekap = collect();
        if ($absensiHariIni) {
            $rekap = DB::table('absensi_detail')
                ->where('absensi_id', $absensiHariIni->id)
                ->where('status', 1)->where('is_full_day', 1)
                ->select('status_absensi_id', DB::raw('COUNT(*) as total'))
                ->groupBy('status_absensi_id')
                ->pluck('total', 'status_absensi_id');
        }

        $totalHadir  = $totalSiswa - $rekap->sum();
        $totalIzin   = $rekap->get(1, 0);
        $totalSakit  = $rekap->get(2, 0);
        $totalAlpha  = $rekap->get(3, 0);
        $totalDispen = $rekap->get(4, 0);

        // Absensi yang perlu diisi: status 1 (Menunggu Pengisian) atau 6 (Perlu Revisi)
        $absensiPerluDiisi = DB::table('absensi as a')
            ->join('status_validasi as sv', 'sv.id', '=', 'a.status_validasi_id')
            ->where('a.kelas_id', $kelas->id)
            ->whereIn('a.status_validasi_id', [1, 6])
            ->where('a.status', 1)
            ->select('a.*', 'sv.nama_status as nama_verifikasi')
            ->orderByDesc('a.tanggal')->get();

        $riwayatAbsensi = DB::table('absensi as a')
            ->join('status_validasi as sv', 'sv.id', '=', 'a.status_validasi_id')
            ->where('a.kelas_id', $kelas->id)->where('a.status', 1)
            ->whereBetween('a.tanggal', [Carbon::today()->subDays(6), Carbon::today()])
            ->select('a.*', 'sv.nama_status as nama_verifikasi')
            ->orderByDesc('a.tanggal')->get();

        $siswaAbsen = collect();
        if ($absensiHariIni) {
            $siswaAbsen = DB::table('absensi_detail as ad')
                ->join('siswa as s', 's.id', '=', 'ad.siswa_id')
                ->join('status_absensi as sa', 'sa.id', '=', 'ad.status_absensi_id')
                ->where('ad.absensi_id', $absensiHariIni->id)
                ->where('ad.status', 1)->where('ad.is_full_day', 1)
                ->select('ad.*', 's.nama_siswa', 's.nis', 'sa.keterangan as status_label')
                ->get();
        }

        return view('dashboard.ketua_kelas', compact(
            'user', 'kelas', 'daftarKelas', 'periodeAktif',
            'totalSiswa', 'totalHadir', 'totalIzin', 'totalSakit', 'totalAlpha', 'totalDispen',
            'absensiHariIni', 'statusVerifHariIni', 'absensiPerluDiisi',
            'riwayatAbsensi', 'siswaAbsen'
        ));
    }
}