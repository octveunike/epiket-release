<?php

namespace App\Http\Controllers\Apps;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Apps\Kelas;
use App\Models\Admin\Guru;
use App\Models\Admin\Siswa;
use App\Models\Apps\PeriodeAkademik;
use Exception;
use Illuminate\Support\Facades\DB;

class KelasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Kelas::with(['waliKelas', 'ketuaKelas', 'periodeAkademik'])
                     ->where('status', '1')
                     ->get();

        return view('Kelas.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $guru         = Guru::where('status', '1')->get();
        $siswa        = Siswa::where('status', '1')
                             ->orderBy('nama_siswa')
                             ->get(['id', 'nama_siswa', 'user_id', 'kelas_id']);
        $periode      = PeriodeAkademik::where('status', '1')->orderByDesc('id')->get();
        $periodeAktif = $periode->first();

        return view('Kelas.create', compact('guru', 'siswa', 'periode', 'periodeAktif'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validation = $request->validate([
            'nama_kelas'         => ['required', 'string', 'max:50'],
            'wali_kelas_id'      => ['nullable', 'integer', 'exists:guru,id', $this->guruHasUserIdRule()],
            'ketua_kelas_id'     => ['nullable', 'integer', 'exists:siswa,id', $this->siswaHasUserIdRule()],
            'periode_akademik_id' => ['nullable', 'integer', 'exists:periode_akademik,id'],
        ]);

        DB::beginTransaction();

        try {
            Kelas::create([
                'nama_kelas'          => $validation['nama_kelas'],
                'wali_kelas_id'       => $validation['wali_kelas_id'] ?? null,
                'ketua_kelas_id'      => $validation['ketua_kelas_id'] ?? null,
                'periode_akademik_id' => $validation['periode_akademik_id'] ?? null,
                'status'              => '1',
                'user_input'          => auth()->user()->id,
                'tanggal_input'       => date('Y-m-d H:i:s'),
            ]);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->route('Kelas.index')->with('success', 'Data Kelas berhasil ditambahkan');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $Kelas   = Kelas::findOrFail($id);
        $guru    = Guru::where('status', '1')->get();
        $siswa   = Siswa::where('status', '1')
                        ->where('kelas_id', $Kelas->id)
                        ->orderBy('nama_siswa')
                        ->get(['id', 'nama_siswa', 'user_id', 'kelas_id']);
        $periode = PeriodeAkademik::where('status', '1')->get();

        return view('Kelas.edit', compact('Kelas', 'guru', 'siswa', 'periode'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validation = $request->validate([
            'nama_kelas'          => ['required', 'string', 'max:50'],
            'wali_kelas_id'       => ['nullable', 'integer', 'exists:guru,id', $this->guruHasUserIdRule()],
            'ketua_kelas_id'      => ['nullable', 'integer', 'exists:siswa,id', $this->siswaHasUserIdRule()],
            'periode_akademik_id' => ['nullable', 'integer', 'exists:periode_akademik,id'],
        ]);

        DB::beginTransaction();

        try {
            $data = Kelas::findOrFail($id);
            $data->update([
                'nama_kelas'          => $validation['nama_kelas'],
                'wali_kelas_id'       => $validation['wali_kelas_id'] ?? null,
                'ketua_kelas_id'      => $validation['ketua_kelas_id'] ?? null,
                'periode_akademik_id' => $validation['periode_akademik_id'] ?? null,
                'user_update'         => auth()->user()->id,
                'tanggal_update'      => date('Y-m-d H:i:s'),
            ]);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->route('Kelas.index')->with('success', 'Data Kelas berhasil diupdate');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $data = Kelas::findOrFail($id);
        $data->update([
            'status'        => '9',
            'user_update'   => auth()->user()->id,
            'tanggal_update'=> date('Y-m-d H:i:s'),
        ]);

        return redirect()->route('Kelas.index')->with('success', 'Data Kelas berhasil dihapus');
    }

    /**
     * Rule: siswa yang dipilih sebagai Ketua Kelas wajib sudah punya user_id.
     * Tanpa ini, role Ketua Kelas tidak akan aktif (derived dari siswa.user_id).
     */
    private function siswaHasUserIdRule(): \Closure
    {
        return function ($attribute, $value, $fail) {
            if ($value && !Siswa::where('id', $value)->value('user_id')) {
                $fail('Siswa yang dipilih belum punya akun login. Edit siswa dan set User (Akun Login) terlebih dahulu.');
            }
        };
    }

    /**
     * Rule: guru yang dipilih sebagai Wali Kelas wajib sudah punya user_id.
     */
    private function guruHasUserIdRule(): \Closure
    {
        return function ($attribute, $value, $fail) {
            if ($value && !Guru::where('id', $value)->value('user_id')) {
                $fail('Guru yang dipilih belum punya akun login. Edit guru dan set User (Akun Login) terlebih dahulu.');
            }
        };
    }
}