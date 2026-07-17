<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ResetDataController extends Controller
{
    /**
     * Halaman konfirmasi reset data (hanya Admin).
     */
    public function index()
    {
        if (!auth()->user()->hasRole('Admin')) {
            abort(403);
        }
        return view('ResetData.index');
    }

    /**
     * Reset seluruh data ke kondisi awal: migrate:fresh --seed.
     * Menghapus SEMUA data operasional (guru, siswa, absensi, dispensasi, dsb.)
     * dan mengembalikan hanya data hasil seeder (role, akun bawaan, kelas, status, periode).
     */
    public function reset(Request $request)
    {
        if (!auth()->user()->hasRole('Admin')) {
            abort(403);
        }

        $request->validate([
            'konfirmasi' => ['required', 'in:RESET'],
            'password'   => ['required', 'string'],
        ], [
            'konfirmasi.required' => 'Ketik RESET untuk konfirmasi.',
            'konfirmasi.in'       => 'Ketik persis "RESET" (huruf besar) untuk melanjutkan.',
            'password.required'   => 'Masukkan password Anda untuk konfirmasi.',
        ]);

        // Verifikasi password admin yang sedang login (dilakukan SEBELUM data di-reset).
        if (!Hash::check($request->password, auth()->user()->password)) {
            return redirect()->back()->with('error', 'Password salah. Reset data dibatalkan.');
        }

        // Drop semua tabel → buat ulang schema → seed data dasar.
        try {
            Artisan::call('migrate:fresh', ['--seed' => true, '--force' => true]);
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Gagal mereset data: ' . $e->getMessage());
        }

        // Akun (termasuk akun ini) sudah dikembalikan ke bawaan → logout & arahkan ke login.
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success',
            'Data berhasil direset ke kondisi awal. Silakan login kembali dengan akun bawaan (admin / admin).');
    }
}
