<?php

namespace App\Http\Controllers\Apps;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Apps\Organisasi;
use App\Models\Apps\SiswaOrganisasi;
use App\Models\Admin\Guru;
use App\Models\Admin\Siswa;
use App\Models\Apps\Kelas;
use App\Imports\OrganisasiImport;
use App\Imports\AnggotaOrganisasiImport;
use Maatwebsite\Excel\Facades\Excel;
use Exception;
use Illuminate\Support\Facades\DB;

class OrganisasiController extends Controller
{
    private function currentUser()
    {
        return auth()->user()->username ?? auth()->user()->nama ?? auth()->user()->email;
    }

    // ──────────────────────────────────────────────
    // CRUD UTAMA ORGANISASI
    // ──────────────────────────────────────────────

    public function index()
    {
        $data = Organisasi::with(['pembina', 'siswaOrganisasi'])
            ->where('status', 1)
            ->orderBy('nama_organisasi')
            ->get();

        return view('Organisasi.index', compact('data'));
    }

    public function create()
    {
        $gurus  = Guru::where('status', 1)->orderBy('nama_guru')->get();
        $siswas = Siswa::with('kelas')->where('status', 1)->orderBy('nama_siswa')->get();

        return view('Organisasi.create', compact('gurus', 'siswas'));
    }

    /**
     * Store: simpan organisasi dulu, lalu redirect ke edit
     * supaya anggota bisa ditambah via inline CRUD.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_organisasi' => ['required', 'string', 'max:100'],
            'pembina_id'      => ['nullable', 'integer', 'exists:guru,id'],
            'keterangan'      => ['nullable', 'string', 'max:255'],
        ]);

        DB::beginTransaction();
        try {
            $organisasi = Organisasi::create([
                'nama_organisasi' => $request->nama_organisasi,
                'pembina_id'      => $request->pembina_id,
                'keterangan'      => $request->keterangan,
                'status'          => 1,
                'user_input'      => $this->currentUser(),
                'tanggal_input'   => now(),
            ]);
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }

        // Redirect ke halaman edit agar bisa langsung kelola anggota
        return redirect()->route('Organisasi.edit', $organisasi->id)
            ->with('success', 'Organisasi berhasil dibuat. Silakan tambahkan anggota.');
    }

    public function show(string $id)
    {
        $Organisasi = Organisasi::with([
            'pembina',
            'siswaOrganisasi.siswa.kelas',
        ])->where('status', 1)->findOrFail($id);

        return view('Organisasi.show', compact('Organisasi'));
    }

    public function edit(string $id)
    {
        $Organisasi = Organisasi::where('status', 1)->findOrFail($id);
        $gurus      = Guru::where('status', 1)->orderBy('nama_guru')->get();

        // Anggota aktif + relasi siswa + kelas
        $anggota = SiswaOrganisasi::with('siswa.kelas')
            ->where('organisasi_id', $id)
            ->where('status', 1)
            ->get();

        // Siswa yang BELUM jadi anggota (untuk dropdown tambah)
        $anggotaSiswaIds = $anggota->pluck('siswa_id')->toArray();
        $siswas = Siswa::with('kelas')
            ->where('status', 1)
            ->whereNotIn('id', $anggotaSiswaIds)
            ->orderBy('nama_siswa')
            ->get();

        $kelass = Kelas::where('status', 1)->orderBy('nama_kelas')->get();

        return view('Organisasi.edit', compact('Organisasi', 'gurus', 'anggota', 'siswas', 'kelass'));
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'nama_organisasi' => ['required', 'string', 'max:100'],
            'pembina_id'      => ['nullable', 'integer', 'exists:guru,id'],
            'keterangan'      => ['nullable', 'string', 'max:255'],
        ]);

        DB::beginTransaction();
        try {
            $organisasi = Organisasi::findOrFail($id);
            $organisasi->update([
                'nama_organisasi' => $request->nama_organisasi,
                'pembina_id'      => $request->pembina_id,
                'keterangan'      => $request->keterangan,
                'user_update'     => $this->currentUser(),
                'tanggal_update'  => now(),
            ]);
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()->route('Organisasi.index', $id)
            ->with('success', 'Data organisasi berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        DB::beginTransaction();
        try {
            Organisasi::findOrFail($id)->update([
                'status'         => 9,
                'user_update'    => $this->currentUser(),
                'tanggal_update' => now(),
            ]);
            SiswaOrganisasi::where('organisasi_id', $id)->update([
                'status'         => 9,
                'user_update'    => $this->currentUser(),
                'tanggal_update' => now(),
            ]);
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->route('Organisasi.index')
            ->with('success', 'Organisasi berhasil dihapus.');
    }

    /**
     * Tambah satu anggota ke organisasi.
     * POST /organisasi/{id}/anggota
     */
    public function anggotaStore(Request $request, string $id)
    {
        $request->validate([
            'siswa_id' => ['required', 'integer', 'exists:siswa,id'],
        ]);

        // Cek apakah siswa ini sudah pernah jadi anggota (soft-deleted)
        $existing = SiswaOrganisasi::where('organisasi_id', $id)
            ->where('siswa_id', $request->siswa_id)
            ->first();

        if ($existing) {
            $existing->update([
                'status'         => 1,
                'user_update'    => $this->currentUser(),
                'tanggal_update' => now(),
            ]);
        } else {
            SiswaOrganisasi::create([
                'organisasi_id' => (int) $id,
                'siswa_id'      => (int) $request->siswa_id,
                'status'        => 1,
                'user_input'    => $this->currentUser(),
                'tanggal_input' => now(),
            ]);
        }

        return redirect()->route('Organisasi.edit', $id)
            ->with('success', 'Anggota berhasil ditambahkan.');
    }

    /**
     * Hapus (soft-delete) satu anggota dari organisasi.
     * DELETE /organisasi/{id}/anggota/{anggotaId}
     */
    public function anggotaDestroy(string $id, string $anggotaId)
    {
        $anggota = SiswaOrganisasi::where('organisasi_id', $id)
            ->where('id', $anggotaId)
            ->firstOrFail();

        $anggota->update([
            'status'         => 9,
            'user_update'    => $this->currentUser(),
            'tanggal_update' => now(),
        ]);

        return redirect()->route('Organisasi.edit', $id)
            ->with('success', 'Anggota berhasil dihapus.');
    }

    /**
     * Import data organisasi dari file Excel.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:2048'],
        ]);

        $importer = new OrganisasiImport();

        DB::beginTransaction();

        try {
            Excel::import($importer, $request->file('file'));
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal import: ' . $e->getMessage());
        }

        $jumlah = $importer->imported;

        if ($jumlah === 0) {
            $detail = !empty($importer->errors)
                ? ' Detail: ' . implode(' | ', array_slice($importer->errors, 0, 5))
                : ' Pastikan file mengikuti format template (header baris pertama, data mulai baris 2).';
            return redirect()->route('Organisasi.index')
                ->with('error', 'Tidak ada data organisasi yang berhasil ditambahkan.' . $detail);
        }

        $msg = "Import berhasil! {$jumlah} data organisasi berhasil ditambahkan.";
        if (!empty($importer->errors)) {
            $msg .= ' (' . count($importer->errors) . ' baris dengan catatan: '
                  . implode(' | ', array_slice($importer->errors, 0, 3)) . ')';
        }

        return redirect()->route('Organisasi.index')->with('success', $msg);
    }

    /**
     * Import daftar anggota organisasi dari file Excel.
     * Kolom: Kelas, Nama Anggota (Nama Siswa).
     */
    public function anggotaImport(Request $request, string $id)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:2048'],
        ]);

        Organisasi::where('status', 1)->findOrFail($id);

        $importer = new AnggotaOrganisasiImport((int) $id, $this->currentUser());

        DB::beginTransaction();

        try {
            Excel::import($importer, $request->file('file'));
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal import: ' . $e->getMessage());
        }

        $jumlah = $importer->imported;

        if ($jumlah === 0) {
            $detail = !empty($importer->errors)
                ? ' Detail: ' . implode(' | ', array_slice($importer->errors, 0, 5))
                : ' Pastikan file mengikuti format template (header baris pertama, data mulai baris 2).';
            return redirect()->route('Organisasi.edit', $id)
                ->with('error', 'Tidak ada anggota yang berhasil ditambahkan.' . $detail);
        }

        $msg = "Import berhasil! {$jumlah} anggota berhasil ditambahkan.";
        if (!empty($importer->errors)) {
            $msg .= ' (' . count($importer->errors) . ' baris dilewati: '
                  . implode(' | ', array_slice($importer->errors, 0, 3)) . ')';
        }

        return redirect()->route('Organisasi.edit', $id)->with('success', $msg);
    }
}