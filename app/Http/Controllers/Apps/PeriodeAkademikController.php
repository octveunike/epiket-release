<?php

namespace App\Http\Controllers\Apps;

use App\Http\Controllers\Controller;
use App\Models\Apps\PeriodeAkademik;
use Illuminate\Http\Request;

class PeriodeAkademikController extends Controller
{
    public function index()
    {
        $data = PeriodeAkademik::orderByDesc('status')->orderByDesc('id')->get();
        return view('PeriodeAkademik.index', compact('data'));
    }

    public function create()
    {
        return view('PeriodeAkademik.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_periode'    => ['required', 'string', 'max:100'],
            'tahun_ajaran'    => ['required', 'string', 'max:20'],
            'semester'        => ['required', 'string', 'max:20'],
            'tanggal_mulai'   => ['required', 'date'],
            'tanggal_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
            'status'          => ['required', 'in:0,1'],
        ]);

        // Cek double aktif
        if ($request->status == 1) {
            $aktif = PeriodeAkademik::where('status', 1)->first();
            if ($aktif) {
                return back()->withInput()
                    ->with('error_aktif', $aktif->nama_periode);
            }
        }

        PeriodeAkademik::create([
            'nama_periode'    => $request->nama_periode,
            'tahun_ajaran'    => $request->tahun_ajaran,
            'semester'        => $request->semester,
            'tanggal_mulai'   => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'status'          => $request->status,
            'user_input'      => auth()->user()->id,
            'tanggal_input'   => now(),
        ]);

        return redirect()->route('PeriodeAkademik.index')
            ->with('success', 'Periode akademik berhasil ditambahkan.');
    }

    public function edit(string $id)
    {
        $PeriodeAkademik = PeriodeAkademik::findOrFail($id);
        return view('PeriodeAkademik.edit', compact('PeriodeAkademik'));
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'nama_periode'    => ['required', 'string', 'max:100'],
            'tahun_ajaran'    => ['required', 'string', 'max:20'],
            'semester'        => ['required', 'string', 'max:20'],
            'tanggal_mulai'   => ['required', 'date'],
            'tanggal_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
            'status'          => ['required', 'in:0,1'],
        ]);

        // Cek double aktif (kecuali periode ini sendiri)
        if ($request->status == 1) {
            $aktif = PeriodeAkademik::where('status', 1)->where('id', '!=', $id)->first();
            if ($aktif) {
                return back()->withInput()
                    ->with('error_aktif', $aktif->nama_periode);
            }
        }

        $periode = PeriodeAkademik::findOrFail($id);
        $periode->update([
            'nama_periode'    => $request->nama_periode,
            'tahun_ajaran'    => $request->tahun_ajaran,
            'semester'        => $request->semester,
            'tanggal_mulai'   => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'status'          => $request->status,
            'user_update'     => auth()->user()->id,
            'tanggal_update'  => now(),
        ]);

        return redirect()->route('PeriodeAkademik.index')
            ->with('success', 'Periode akademik berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $periode = PeriodeAkademik::findOrFail($id);

        if ($periode->status == 1) {
            return redirect()->back()
                ->with('error', 'Periode aktif tidak dapat dihapus. Ubah status ke Non-Aktif terlebih dahulu.');
        }

        if (PeriodeAkademik::count() <= 1) {
            return redirect()->back()
                ->with('error', 'Minimal harus ada 1 periode akademik.');
        }

        $periode->delete();

        return redirect()->route('PeriodeAkademik.index')
            ->with('success', 'Periode akademik berhasil dihapus.');
    }

    public function aktivasi(string $id)
    {
        // Cek apakah sudah ada periode aktif lain
        $aktif = PeriodeAkademik::where('status', 1)->where('id', '!=', $id)->first();

        if ($aktif) {
            return redirect()->back()
                ->with('error_aktif', 'Sudah ada periode aktif: <strong>' . $aktif->nama_periode . '</strong>. Non-aktifkan terlebih dahulu.');
        }

        $periode = PeriodeAkademik::findOrFail($id);
        $periode->update([
            'status'         => 1,
            'user_update'    => auth()->user()->id,
            'tanggal_update' => now(),
        ]);

        return redirect()->route('PeriodeAkademik.index')
            ->with('success', $periode->nama_periode . ' berhasil diaktifkan.');
    }

    public function nonaktifkan(string $id)
    {
        $periode = PeriodeAkademik::findOrFail($id);
        $periode->update([
            'status'         => 0,
            'user_update'    => auth()->user()->id,
            'tanggal_update' => now(),
        ]);

        return redirect()->route('PeriodeAkademik.index')
            ->with('success', $periode->nama_periode . ' berhasil dinonaktifkan.');
    }
}