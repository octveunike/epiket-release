<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\Kegiatan;
use Exception;
use Illuminate\Support\Facades\DB;


class KegiatanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Kegiatan::where('status', '1')->get();
        return view('Kegiatan.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('Kegiatan.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validation = $request->validate([
            'title' => ['required', 'string'],
            'tipe_kegiatan' => ['required', 'string'],
            'tgl_awal' => ['required', 'date'],
            'tgl_akhir' => ['required', 'date'],
            'deskripsi' => ['required', 'string'],
            'sampul' => ['required', 'image', 'mimes:jpg,jpeg,png'],
            'kuota_peserta' => ['required', 'integer'],
            'penyelenggara' => ['required', 'string'],
        ]);

        DB::beginTransaction();
        $return_status = 'Valid';

        try {
            $data = Kegiatan::create([
                'title' => $validation['title'],
                'tipe_kegiatan' => $validation['tipe_kegiatan'],
                'tgl_awal' => $validation['tgl_awal'],
                'tgl_akhir' => $validation['tgl_akhir'],
                'deskripsi' => $validation['deskripsi'],
                'kuota_peserta' => $validation['kuota_peserta'],
                'penyelenggara' => $validation['penyelenggara'],
                'status' => '1',
                'user_input' => auth()->user()->id,
                'tanggal_input' => date('Y-m-d H:i:s'),
            ]);

            if ($request->hasFile('sampul')) {
                $file = $request->file('sampul');
                $filename = 'kegiatan_' . $data->id;
                $path = $file->storeAs('public/kegiatan', $filename); // Simpan ke storage
        
                // Buat URI untuk database
                $file_uri = 'storage/app/' . $path;
        
                // Update kolom sampul_uri
                $data->update(['sampul' => $file_uri]);
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e);
        }

        return redirect()->route('Kegiatan.index')->with('success', 'Daftar Kegiatan berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $Kegiatan = Kegiatan::findOrFail($id);
        return view('Kegiatan.edit', compact('Kegiatan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validation = $request->validate([
            'title' => ['required', 'string'],
            'tipe_kegiatan' => ['required', 'string'],
            'tgl_awal' => ['required', 'date'],
            'tgl_akhir' => ['required', 'date'],
            'deskripsi' => ['required', 'string'],
            'sampul' => ['required', 'image', 'mimes:jpg,jpeg,png'],
            'kuota_peserta' => ['required', 'integer'],
            'penyelenggara' => ['required', 'string'],
        ]);
        DB::beginTransaction();
        $return_status = 'Valid';

        try {
            $data = Kegiatan::findOrFail($id);
            $data->update([
                'title' => $validation['title'],
                'tipe_kegiatan' => $validation['tipe_kegiatan'],
                'tgl_awal' => $validation['tgl_awal'],
                'tgl_akhir' => $validation['tgl_akhir'],
                'deskripsi' => $validation['deskripsi'],
                'kuota_peserta' => $validation['kuota_peserta'],
                'penyelenggara' => $validation['penyelenggara'],
                'status' => '1',
                'user_update' => auth()->user()->id,
                'tanggal_update' => date('Y-m-d H:i:s'),
            ]);

            if ($request->hasFile('sampul')) {
                $file = $request->file('sampul');
                $filename = 'kegiatan_' . $data->id;
                $path = $file->storeAs('public/kegiatan', $filename); // Simpan ke storage
        
                // Buat URI untuk database
                $file_uri = 'storage/app/' . $path;
        
                // Update kolom sampul_uri
                $data->update(['sampul' => $file_uri]);
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e);
        }

        return redirect()->route('Kegiatan.index')->with('success', 'Daftar Kegiatan berhasil diupdate');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $data = Kegiatan::findOrFail($id);
        $data->update([
            'status' => '9',
            'user_update' => auth()->user()->id,
            'tanggal_update' => date('Y-m-d H:i:s'),
        ]);
        return redirect()->route('Kegiatan.index')->with('success', 'Daftar Kegiatan berhasil dihapus');
    }
}