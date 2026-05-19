<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\Staff;
use App\Models\User;
use App\Imports\StaffImport;
use Maatwebsite\Excel\Facades\Excel;
use Exception;
use Illuminate\Support\Facades\DB;

class StaffController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Staff::with('user')->where('status', '1')->get();
        return view('Staff.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = User::all();
        return view('Staff.create', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validation = $request->validate([
            'nama_staff' => ['required', 'string', 'max:100'],
            'user_id'    => ['nullable', 'integer', 'exists:users,id'],
        ]);

        DB::beginTransaction();

        try {
            Staff::create([
                'nama_staff'   => $validation['nama_staff'],
                'user_id'      => $validation['user_id'] ?? null,
                'status'       => '1',
                'user_input'   => auth()->user()->id,
                'tanggal_input'=> date('Y-m-d H:i:s'),
            ]);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->route('Staff.index')->with('success', 'Data Staff berhasil ditambahkan');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $Staff = Staff::findOrFail($id);
        $users = User::all();
        return view('Staff.edit', compact('Staff', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validation = $request->validate([
            'nama_staff' => ['required', 'string', 'max:100'],
            'user_id'    => ['nullable', 'integer', 'exists:users,id'],
        ]);

        DB::beginTransaction();

        try {
            $data = Staff::findOrFail($id);
            $data->update([
                'nama_staff'    => $validation['nama_staff'],
                'user_id'       => $validation['user_id'] ?? null,
                'user_update'   => auth()->user()->id,
                'tanggal_update'=> date('Y-m-d H:i:s'),
            ]);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->route('Staff.index')->with('success', 'Data Staff berhasil diupdate');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $data = Staff::findOrFail($id);
        $data->update([
            'status'        => '9',
            'user_update'   => auth()->user()->id,
            'tanggal_update'=> date('Y-m-d H:i:s'),
        ]);

        return redirect()->route('Staff.index')->with('success', 'Data Staff berhasil dihapus');
    }

    /**
     * Import data staff dari file Excel.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:2048'],
        ]);

        $importer = new StaffImport();

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
            return redirect()->route('Staff.index')
                ->with('error', 'Tidak ada data staff yang berhasil ditambahkan.' . $detail);
        }

        $msg = "Import berhasil! {$jumlah} data staff berhasil ditambahkan.";
        if (!empty($importer->errors)) {
            $msg .= ' (' . count($importer->errors) . ' baris dilewati: '
                  . implode(' | ', array_slice($importer->errors, 0, 3)) . ')';
        }

        return redirect()->route('Staff.index')->with('success', $msg);
    }
}