<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\Siswa;
use App\Models\Apps\Kelas;
use App\Models\User;
use App\Imports\SiswaImport;
use App\Exports\SiswaExport;
use Maatwebsite\Excel\Facades\Excel;
use Exception;
use Illuminate\Support\Facades\DB;

class SiswaController extends Controller
{
    public function index()
    {
        $data = Siswa::with(['kelas', 'user'])->where('status', '1')->get();
        return view('Siswa.index', compact('data'));
    }

    public function create()
    {
        $kelas = Kelas::where('status', '1')->get();
        $users = User::where('status', 1)->orderBy('nama')->get();
        return view('Siswa.create', compact('kelas', 'users'));
    }

    public function store(Request $request)
    {
        $validation = $request->validate([
            'nis'            => ['required', 'string', 'max:50', 'unique:siswa,nis'],
            'nama_siswa'     => ['required', 'string', 'max:100'],
            'jenis_kelamin'  => ['required', 'in:L,P'],
            'tanggal_masuk'  => ['required', 'date'],
            'kelas_id'       => ['required', 'integer'],
            'status_siswa_id'=> ['required', 'integer'],
            'user_id'        => ['nullable', 'integer', 'exists:users,id'],
        ]);

        DB::beginTransaction();

        try {
            Siswa::create([
                'nis'             => $validation['nis'],
                'nama_siswa'      => $validation['nama_siswa'],
                'jenis_kelamin'   => $validation['jenis_kelamin'],
                'tanggal_masuk'   => $validation['tanggal_masuk'],
                'kelas_id'        => $validation['kelas_id'],
                'status_siswa_id' => $validation['status_siswa_id'],
                'user_id'         => $validation['user_id'] ?? null,
                'status'          => '1',
                'user_input'      => auth()->user()->id,
                'tanggal_input'   => now(),
            ]);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->route('Siswa.index')->with('success', 'Data Siswa berhasil ditambahkan');
    }

    public function edit(string $id)
    {
        $Siswa = Siswa::findOrFail($id);
        $kelas = Kelas::where('status', '1')->get();
        $users = User::where('status', 1)->orderBy('nama')->get();
        return view('Siswa.edit', compact('Siswa', 'kelas', 'users'));
    }

    public function update(Request $request, string $id)
    {
        $validation = $request->validate([
            'nis'            => ['required', 'string', 'max:50', 'unique:siswa,nis,' . $id],
            'nama_siswa'     => ['required', 'string', 'max:100'],
            'jenis_kelamin'  => ['required', 'in:L,P'],
            'tanggal_masuk'  => ['required', 'date'],
            'kelas_id'       => ['required', 'integer'],
            'status_siswa_id'=> ['required', 'integer'],
            'user_id'        => ['nullable', 'integer', 'exists:users,id'],
        ]);

        DB::beginTransaction();

        try {
            $data = Siswa::findOrFail($id);
            $data->update([
                'nis'             => $validation['nis'],
                'nama_siswa'      => $validation['nama_siswa'],
                'jenis_kelamin'   => $validation['jenis_kelamin'],
                'tanggal_masuk'   => $validation['tanggal_masuk'],
                'kelas_id'        => $validation['kelas_id'],
                'status_siswa_id' => $validation['status_siswa_id'],
                'user_id'         => $validation['user_id'] ?? null,
                'user_update'     => auth()->user()->id,
                'tanggal_update'  => now(),
            ]);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }

        // Redirect balik ke halaman Kelas kalau datang dari sana
        if ($request->return_to === 'kelas_edit' && $request->kelas_id) {
            return redirect()->route('Kelas.edit', $request->kelas_id)
                ->with('success', 'Data Siswa berhasil diupdate');
        }
        if ($request->return_to === 'kelas_create') {
            return redirect()->route('Kelas.create')
                ->with('success', 'Data Siswa berhasil diupdate');
        }

        return redirect()->route('Siswa.index')->with('success', 'Data Siswa berhasil diupdate');
    }

    public function destroy(string $id)
    {
        $data = Siswa::findOrFail($id);
        $data->update([
            'status'         => '9',
            'user_update'    => auth()->user()->id,
            'tanggal_update' => now(),
        ]);

        return redirect()->route('Siswa.index')->with('success', 'Data Siswa berhasil dihapus');
    }

    /**
     * Unduh seluruh data siswa aktif sebagai Excel (format sama dengan template import).
     */
    public function export()
    {
        if (!auth()->user()->hasRole('Admin')) {
            abort(403);
        }
        return Excel::download(new SiswaExport, 'Data_Siswa_' . now()->format('Ymd_His') . '.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:2048'],
        ]);

        $importer = new SiswaImport();

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
            return redirect()->route('Siswa.index')
                ->with('error', 'Tidak ada data siswa yang berhasil ditambahkan.' . $detail);
        }

        $msg = "Import berhasil! {$jumlah} data siswa berhasil ditambahkan.";
        if (!empty($importer->errors)) {
            $msg .= ' (' . count($importer->errors) . ' baris dilewati: '
                  . implode(' | ', array_slice($importer->errors, 0, 3)) . ')';
        }

        return redirect()->route('Siswa.index')->with('success', $msg);
    }
}