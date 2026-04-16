<?php

namespace App\Http\Controllers\Apps;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Apps\DaftarTamu;
use Exception;
use Illuminate\Support\Facades\DB;

class DaftarTamuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = DaftarTamu::where('status', '1')->get();
        return view('DaftarTamu.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('DaftarTamu.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validation = $request->validate([
            'tanggal_kunjungan'=> ['required', 'date'],
            'nama'=> ['required', 'string', 'max:255'],
            'lembaga_organisasi'=> ['nullable', 'string', 'max:255'],
            'alamat'=> ['nullable', 'string'],
            'orang_yang_dituju'=> ['nullable', 'string', 'max:255'],
            'tujuan_kunjungan'=> ['nullable', 'string'],
        ]);

        DB::beginTransaction();

        try {
            DaftarTamu::create([
                'tanggal_kunjungan' => $validation['tanggal_kunjungan'],
                'nama' => $validation['nama'],
                'lembaga_organisasi' => $validation['lembaga_organisasi'] ?? null,
                'alamat' => $validation['alamat'] ?? null,
                'orang_yang_dituju' => $validation['orang_yang_dituju'] ?? null,
                'tujuan_kunjungan' => $validation['tujuan_kunjungan'] ?? null,
                'status'       => '1',
                'user_input'   => auth()->user()->id,
                'tanggal_input'=> date('Y-m-d H:i:s'),
            ]);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->route('DaftarTamu.index')->with('success', 'Data Daftar Tamu berhasil ditambahkan');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $DaftarTamu  = DaftarTamu::findOrFail($id);
        return view('DaftarTamu.edit', compact('DaftarTamu'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validation = $request->validate([
            'tanggal_kunjungan'=> ['required', 'date'],
            'nama'=> ['required', 'string', 'max:255'],
            'lembaga_organisasi'=> ['nullable', 'string', 'max:255'],
            'alamat'=> ['nullable', 'string'],
            'orang_yang_dituju'=> ['nullable', 'string', 'max:255'],
            'tujuan_kunjungan'=> ['nullable', 'string'],
        ]);

        DB::beginTransaction();

        try {
            $data = DaftarTamu::findOrFail($id);
            $data->update([
                'tanggal_kunjungan' => $validation['tanggal_kunjungan'],
                'nama' => $validation['nama'],
                'lembaga_organisasi' => $validation['lembaga_organisasi'] ?? null,
                'alamat' => $validation['alamat'] ?? null,
                'orang_yang_dituju' => $validation['orang_yang_dituju'] ?? null,
                'tujuan_kunjungan' => $validation['tujuan_kunjungan'] ?? null,
                'user_update'   => auth()->user()->id,
                'tanggal_update'=> date('Y-m-d H:i:s'),
            ]);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->route('DaftarTamu.index')->with('success', 'Data Daftar Tamu berhasil diupdate');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $data = DaftarTamu::findOrFail($id);
        $data->update([
            'status'        => '9',
            'user_update'   => auth()->user()->id,
            'tanggal_update'=> date('Y-m-d H:i:s'),
        ]);

        return redirect()->route('DaftarTamu.index')->with('success', 'Data Daftar Tamu berhasil dihapus');
    }
}