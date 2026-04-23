<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\Guru;
use App\Models\User;
use App\Imports\GuruImport;
use Maatwebsite\Excel\Facades\Excel;
use Exception;
use Illuminate\Support\Facades\DB;

class GuruController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Guru::with('user')->where('status', '1')->get();
        return view('Guru.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = User::where('status', 1)->orderBy('nama')->get();
        return view('Guru.create', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validation = $request->validate([
            'nip'           => ['required', 'string', 'max:50', 'unique:guru,nip'],
            'nama_guru'     => ['required', 'string', 'max:100'],
            'mata_pelajaran'=> ['nullable', 'string', 'max:255'],
            'user_id'       => ['nullable', 'integer', 'exists:users,id'],
        ]);

        DB::beginTransaction();

        try {
            Guru::create([
                'nip'           => $validation['nip'],
                'nama_guru'     => $validation['nama_guru'],
                'mata_pelajaran'=> $validation['mata_pelajaran'] ?? null,
                'user_id'       => $validation['user_id'] ?? null,
                'status'        => '1',
                'user_input'    => auth()->user()->id,
                'tanggal_input' => date('Y-m-d H:i:s'),
            ]);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->route('Guru.index')->with('success', 'Data Guru berhasil ditambahkan');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $Guru  = Guru::findOrFail($id);
        $users = User::where('status', 1)->orderBy('nama')->get();
        return view('Guru.edit', compact('Guru', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validation = $request->validate([
            'nip'           => ['required', 'string', 'max:50', 'unique:guru,nip,' . $id],
            'nama_guru'     => ['required', 'string', 'max:100'],
            'mata_pelajaran'=> ['nullable', 'string', 'max:255'],
            'user_id'       => ['nullable', 'integer', 'exists:users,id'],
        ]);

        DB::beginTransaction();

        try {
            $data = Guru::findOrFail($id);
            $data->update([
                'nip'           => $validation['nip'],
                'nama_guru'     => $validation['nama_guru'],
                'mata_pelajaran'=> $validation['mata_pelajaran'] ?? null,
                'user_id'       => $validation['user_id'] ?? null,
                'user_update'   => auth()->user()->id,
                'tanggal_update'=> date('Y-m-d H:i:s'),
            ]);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }

        // Redirect balik ke halaman Kelas edit kalau datang dari sana
        if ($request->return_to === 'kelas_edit' && $request->kelas_id) {
            return redirect()->route('Kelas.edit', $request->kelas_id)
                ->with('success', 'Data Guru berhasil diupdate');
        }

        return redirect()->route('Guru.index')->with('success', 'Data Guru berhasil diupdate');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $data = Guru::findOrFail($id);
        $data->update([
            'status'        => '9',
            'user_update'   => auth()->user()->id,
            'tanggal_update'=> date('Y-m-d H:i:s'),
        ]);

        return redirect()->route('Guru.index')->with('success', 'Data Guru berhasil dihapus');
    }

    /**
     * Import data guru dari file Excel.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:2048'],
        ]);

        DB::beginTransaction();

        try {
            $sebelum = Guru::where('status', '1')->count();

            Excel::import(new GuruImport, $request->file('file'));

            $sesudah = Guru::where('status', '1')->count();
            $jumlah  = $sesudah - $sebelum;

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal import: ' . $e->getMessage());
        }

        return redirect()->route('Guru.index')->with('success', 'Import berhasil! ' . $jumlah . ' data guru berhasil ditambahkan.');
    }
}