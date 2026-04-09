<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\Siswa;
use App\Models\Apps\Kelas;
use Exception;
use Illuminate\Support\Facades\DB;

class SiswaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Siswa::where('status', '1')->get();
        return view('Siswa.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $kelas = Kelas::where('status', '1')->get();
        return view('Siswa.create', compact('kelas'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validation = $request->validate([
            'nis'            => ['required', 'string', 'max:50', 'unique:siswa,nis'],
            'nama_siswa'     => ['required', 'string', 'max:100'],
            'jenis_kelamin'  => ['required', 'in:L,P'],
            'tanggal_masuk'  => ['required', 'date'],
            'kelas_id'       => ['required', 'integer'],
            'status_siswa_id'=> ['required', 'integer'],
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
                'status'          => '1',
                'user_input'      => auth()->user()->id,
                'tanggal_input'   => date('Y-m-d H:i:s'),
            ]);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->route('Siswa.index')->with('success', 'Data Siswa berhasil ditambahkan');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $Siswa = Siswa::findOrFail($id);
        $kelas = Kelas::where('status', '1')->get();
        return view('Siswa.edit', compact('Siswa', 'kelas'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validation = $request->validate([
            'nis'            => ['required', 'string', 'max:50', 'unique:siswa,nis,' . $id],
            'nama_siswa'     => ['required', 'string', 'max:100'],
            'jenis_kelamin'  => ['required', 'in:L,P'],
            'tanggal_masuk'  => ['required', 'date'],
            'kelas_id'       => ['required', 'integer'],
            'status_siswa_id'=> ['required', 'integer'],
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
                'user_update'     => auth()->user()->id,
                'tanggal_update'  => date('Y-m-d H:i:s'),
            ]);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->route('Siswa.index')->with('success', 'Data Siswa berhasil diupdate');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $data = Siswa::findOrFail($id);
        $data->update([
            'status'         => '9',
            'user_update'    => auth()->user()->id,
            'tanggal_update' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->route('Siswa.index')->with('success', 'Data Siswa berhasil dihapus');
    }
}