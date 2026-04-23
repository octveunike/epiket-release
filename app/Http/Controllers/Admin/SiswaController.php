<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\Siswa;
use App\Models\Apps\Kelas;
use App\Models\User;
use App\Imports\SiswaImport;
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

        // Redirect balik ke halaman Kelas edit kalau datang dari sana
        if ($request->return_to === 'kelas_edit' && $request->kelas_id) {
            return redirect()->route('Kelas.edit', $request->kelas_id)
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

    public function import(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:2048'],
        ]);

        DB::beginTransaction();

        try {
            $sebelum = Siswa::where('status', '1')->count();

            Excel::import(new SiswaImport, $request->file('file'));

            $sesudah = Siswa::where('status', '1')->count();
            $jumlah  = $sesudah - $sebelum;

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal import: ' . $e->getMessage());
        }

        return redirect()->route('Siswa.index')->with('success', 'Import berhasil! ' . $jumlah . ' data siswa berhasil ditambahkan.');
    }
}