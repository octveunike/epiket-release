<?php

namespace App\Http\Controllers\UserManagement;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserManagement\Roles;
use App\Models\UserManagement\UserRole;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserManagementController extends Controller
{
    /**
     * Display a listing of users.
     * Hanya tampilkan user dengan status = 1 (aktif)
     */
    public function index()
    {
        if (!auth()->user()->hasRole('Admin')) {
            abort(403);
        }
        $data = User::with('roles')->where('status', 1)->get();
        return view('UserManagement.index', compact('data'));
    }

    /**
     * Show form create user.
     */
    public function create()
    {
        if (!auth()->user()->hasRole('Admin')) {
            abort(403);
        }
        $roles = Roles::where('status', 1)->get();
        return view('UserManagement.create', compact('roles'));
    }

    /**
     * Store new user.
     * Kolom user_input = varchar(100), diisi username/nama yang sedang login
     */
    public function store(Request $request)
    {
        if (!auth()->user()->hasRole('Admin')) {
            abort(403);
        }
        $request->validate([
            'nama'       => ['required', 'string', 'max:100'],
            'username'   => ['required', 'string', 'max:50', 'unique:users,username'],
            'email'      => ['required', 'email', 'max:100', 'unique:users,email'],
            'password'   => ['required', 'string', 'min:6', 'confirmed'],
            'role_ids'   => ['required', 'array', 'min:1'],
            'role_ids.*' => ['integer', 'exists:roles,id'],
        ], [
            'role_ids.required' => 'Pilih minimal 1 role untuk akun ini.',
            'role_ids.min'      => 'Pilih minimal 1 role untuk akun ini.',
        ]);

        $user = auth()->user();
        $currentUser = $user->username
            ?? $user->nama
            ?? $user->email
            ?? 'system';

        DB::beginTransaction();

        try {
            $user = User::create([
                'nama'          => $request->nama,
                'username'      => $request->username,
                'email'         => $request->email,
                'password'      => Hash::make($request->password),
                'status'        => 1,
                'user_input'    => $currentUser,
                'tanggal_input' => date('Y-m-d H:i:s'),
            ]);

            // Insert ke user_role untuk setiap role yang dicentang
            foreach ($request->input('role_ids', []) as $roleId) {
                UserRole::create([
                    'user_id'       => $user->id,
                    'role_id'       => (int) $roleId,
                    'status'        => 1,
                    'user_input'    => $currentUser,
                    'tanggal_input' => date('Y-m-d H:i:s'),
                ]);
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->route('UserManagement.index')->with('success', 'User berhasil ditambahkan');
    }

    /**
     * Inline create user dari form lain (Siswa/Guru) — return JSON.
     * Tidak assign role apapun; role Ketua Kelas / Wali Kelas akan terpicu
     * otomatis lewat kelas.ketua_kelas_id / kelas.wali_kelas_id.
     */
    public function storeInline(Request $request)
    {
        if (!auth()->user()->hasRole('Admin')) {
            return response()->json(['message' => 'Hanya Admin yang dapat membuat user.'], 403);
        }

        $validated = $request->validate([
            'nama'     => ['required', 'string', 'max:100'],
            'username' => ['required', 'string', 'max:50', 'unique:users,username'],
            'email'    => ['required', 'email', 'max:100', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
        ]);

        $actor       = auth()->user();
        $currentUser = $actor->username ?? $actor->nama ?? $actor->email ?? 'system';

        $user = User::create([
            'nama'          => $validated['nama'],
            'username'      => $validated['username'],
            'email'         => $validated['email'],
            'password'      => Hash::make($validated['password']),
            'status'        => 1,
            'user_input'    => $currentUser,
            'tanggal_input' => date('Y-m-d H:i:s'),
        ]);

        return response()->json([
            'id'       => $user->id,
            'nama'     => $user->nama,
            'username' => $user->username,
        ], 201);
    }

    /**
     * Show form edit user.
     * $activeRoles = array of role_id yang aktif (status=1) milik user ini
     */
    public function edit(string $id)
    {
        $authUser = auth()->user();
        $isAdmin  = $authUser->hasRole('Admin');

        if (!$isAdmin && (int) $authUser->id !== (int) $id) {
            abort(403, 'Anda hanya dapat mengedit akun Anda sendiri.');
        }

        $User        = User::with('roles')->findOrFail($id);
        $roles       = Roles::where('status', 1)->get();
        $activeRoles = $User->roles->pluck('id')->toArray();
        $canEditRole = $isAdmin;

        return view('UserManagement.edit', compact('User', 'roles', 'activeRoles', 'canEditRole'));
    }

    /**
     * Update user.
     * Password hanya diupdate jika field diisi.
     * Role: nonaktifkan semua yang lama, insert ulang yang baru dipilih.
     */
    public function update(Request $request, string $id)
    {
        $authUser = auth()->user();
        $isAdmin  = $authUser->hasRole('Admin');

        if (!$isAdmin && (int) $authUser->id !== (int) $id) {
            abort(403, 'Anda hanya dapat mengedit akun Anda sendiri.');
        }

        $rules = [
            'nama'     => ['required', 'string', 'max:100'],
            'username' => ['required', 'string', 'max:50', 'unique:users,username,' . $id],
            'email'    => ['required', 'email', 'max:100', 'unique:users,email,' . $id],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
        ];
        $messages = [];
        if ($isAdmin) {
            $rules['role_ids']   = ['required', 'array', 'min:1'];
            $rules['role_ids.*'] = ['integer', 'exists:roles,id'];
            $messages['role_ids.required'] = 'Pilih minimal 1 role untuk akun ini.';
            $messages['role_ids.min']      = 'Pilih minimal 1 role untuk akun ini.';
        }
        $request->validate($rules, $messages);

        $currentUser = $authUser->username
            ?? $authUser->nama
            ?? $authUser->email
            ?? 'system';

        DB::beginTransaction();

        try {
            $user = User::findOrFail($id);

            $updateData = [
                'nama'           => $request->nama,
                'username'       => $request->username,
                'email'          => $request->email,
                'user_update'    => $currentUser,
                'tanggal_update' => date('Y-m-d H:i:s'),
            ];

            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($request->password);
            }

            $user->update($updateData);

            // Role hanya bisa diubah oleh Admin.
            if ($isAdmin) {
                UserRole::where('user_id', $id)->update([
                    'status'         => 9,
                    'user_update'    => $currentUser,
                    'tanggal_update' => date('Y-m-d H:i:s'),
                ]);

                foreach ($request->input('role_ids', []) as $roleId) {
                    UserRole::create([
                        'user_id'       => (int) $id,
                        'role_id'       => (int) $roleId,
                        'status'        => 1,
                        'user_input'    => $currentUser,
                        'tanggal_input' => date('Y-m-d H:i:s'),
                    ]);
                }
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }

        $passwordChanged = $request->filled('password');

        // Non-admin yang ubah password sendiri → balik ke halaman edit
        // dengan flash khusus untuk memicu modal "Password Berhasil Diubah".
        if (!$isAdmin && $passwordChanged) {
            return redirect()->route('UserManagement.edit', $id)
                ->with('password_changed', true);
        }

        $redirectRoute = $isAdmin ? 'UserManagement.index' : 'admin.index';
        return redirect()->route($redirectRoute)->with('success', 'Data berhasil diperbarui.');
    }

    /**
     * Soft delete user (status = 9).
     * Semua user_role user ini ikut dinonaktifkan.
     */
    public function destroy(string $id)
    {
        if (!auth()->user()->hasRole('Admin')) {
            abort(403);
        }
        $currentUser = auth()->user()->username ?? auth()->user()->nama ?? auth()->user()->email;

        DB::beginTransaction();

        try {
            $user = User::findOrFail($id);
            $user->update([
                'status'         => 9,
                'user_update'    => $currentUser,
                'tanggal_update' => date('Y-m-d H:i:s'),
            ]);

            // Nonaktifkan semua role user ini
            UserRole::where('user_id', $id)->update([
                'status'         => 9,
                'user_update'    => $currentUser,
                'tanggal_update' => date('Y-m-d H:i:s'),
            ]);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->route('UserManagement.index')->with('success', 'User berhasil dihapus');
    }
}