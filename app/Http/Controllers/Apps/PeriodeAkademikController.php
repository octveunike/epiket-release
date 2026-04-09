<?php

namespace App\Http\Controllers\Apps;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Apps\PeriodeAkademik;
use Exception;
use Illuminate\Support\Facades\DB;

class PeriodeAkademikController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = PeriodeAkademik::where('status', '1')->get();
        return view('PeriodeAkademik.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('PeriodeAkademik.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validation = $request->validate([
            'nama_periode'=> ['required', 'string', 'max:100'],
            'tahun_ajaran'=> ['required', 'string', 'max:100'],
            'semester'=> ['required', 'string', 'max:100'],
            'tanggal_mulai'  => ['required', 'date'],
            'tanggal_selesai'  => ['required', 'date'],
        ]);

        DB::beginTransaction();

        try {
            PeriodeAkademik::create([
                'nama_periode' => $validation['nama_periode'],
                'tahun_ajaran' => $validation['tahun_ajaran'],
                'semester'     => $validation['semester'],
                'tanggal_mulai'=> $validation['tanggal_mulai'],
                'tanggal_selesai' => $validation['tanggal_selesai'],
                'status'       => '1',
                'user_input'   => auth()->user()->id,
                'tanggal_input'=> date('Y-m-d H:i:s'),
            ]);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->route('PeriodeAkademik.index')->with('success', 'Data Periode Akademik berhasil ditambahkan');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $PeriodeAkademik  = PeriodeAkademik::findOrFail($id);
        return view('PeriodeAkademik.edit', compact('PeriodeAkademik'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validation = $request->validate([
            'nama_periode'=> ['required', 'string', 'max:100'],
            'tahun_ajaran'=> ['required', 'string', 'max:100'],
            'semester'=> ['required', 'string', 'max:100'],
            'tanggal_mulai'  => ['required', 'date'],
            'tanggal_selesai'  => ['required', 'date'],
        ]);

        DB::beginTransaction();

        try {
            $data = PeriodeAkademik::findOrFail($id);
            $data->update([
                'nama_periode' => $validation['nama_periode'],
                'tahun_ajaran' => $validation['tahun_ajaran'],
                'semester'     => $validation['semester'],
                'tanggal_mulai'=> $validation['tanggal_mulai'],
                'tanggal_selesai' => $validation['tanggal_selesai'],
                'user_update'   => auth()->user()->id,
                'tanggal_update'=> date('Y-m-d H:i:s'),
            ]);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->route('PeriodeAkademik.index')->with('success', 'Data Periode Akademik berhasil diupdate');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $data = PeriodeAkademik::findOrFail($id);
        $data->update([
            'status'        => '9',
            'user_update'   => auth()->user()->id,
            'tanggal_update'=> date('Y-m-d H:i:s'),
        ]);

        return redirect()->route('PeriodeAkademik.index')->with('success', 'Data Periode Akademik berhasil dihapus');
    }
}