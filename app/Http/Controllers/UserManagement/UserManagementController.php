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

        // Guru & Siswa yang BELUM punya akun login — untuk dibuatkan akunnya.
        $guruList = DB::table('guru')
            ->whereNull('user_id')->where('status', 1)
            ->orderBy('nama_guru')
            ->get(['id', 'nama_guru']);

        // Siswa + info kelas & ketua kelas saat ini (untuk deteksi konflik ketua di sisi klien).
        $siswaList = DB::table('siswa as s')
            ->leftJoin('kelas as k', 'k.id', '=', 's.kelas_id')
            ->leftJoin('siswa as ck', 'ck.id', '=', 'k.ketua_kelas_id')
            ->whereNull('s.user_id')->where('s.status', 1)
            ->orderBy('s.nama_siswa')
            ->get([
                's.id', 's.nama_siswa', 's.kelas_id',
                'k.nama_kelas', 'k.ketua_kelas_id',
                'ck.nama_siswa as current_ketua_nama',
            ]);

        // Kelas + wali saat ini — untuk pemilihan kelas Wali Kelas & deteksi konflik.
        $kelasList = DB::table('kelas as k')
            ->leftJoin('guru as wg', 'wg.id', '=', 'k.wali_kelas_id')
            ->where('k.status', 1)
            ->orderBy('k.nama_kelas')
            ->get(['k.id', 'k.nama_kelas', 'k.wali_kelas_id', 'wg.nama_guru as current_wali_nama']);

        $ketuaRoleId = $this->ketuaRoleId();
        $waliRoleId  = $this->waliRoleId();

        return view('UserManagement.create', compact('roles', 'guruList', 'siswaList', 'kelasList', 'ketuaRoleId', 'waliRoleId'));
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
            'kategori'   => ['required', 'in:guru,siswa'],
            'ref_id'     => ['required', 'integer'],
            'username'   => ['required', 'string', 'max:50', 'unique:users,username'],
            'email'      => ['required', 'email', 'max:100', 'unique:users,email'],
            'password'   => ['required', 'string', 'min:6', 'confirmed'],
            'role_ids'   => ['required', 'array', 'min:1'],
            'role_ids.*' => ['integer', 'exists:roles,id'],
        ], [
            'kategori.required' => 'Pilih kategori (Guru atau Siswa) terlebih dahulu.',
            'ref_id.required'   => 'Pilih data guru/siswa yang akan dibuatkan akun.',
            'role_ids.required' => 'Pilih minimal 1 role untuk akun ini.',
            'role_ids.min'      => 'Pilih minimal 1 role untuk akun ini.',
        ]);

        $tipe        = $request->kategori;
        $refId       = (int) $request->ref_id;
        $roleIds     = array_map('intval', $request->input('role_ids', []));
        $ketuaRoleId = $this->ketuaRoleId();
        $waliRoleId  = $this->waliRoleId();
        $wantsKetua  = $ketuaRoleId && in_array($ketuaRoleId, $roleIds, true);
        $wantsWali   = $waliRoleId && in_array($waliRoleId, $roleIds, true);

        // ── Ambil & validasi data guru/siswa (harus ada & belum punya akun) ──
        $ref = DB::table($tipe)->where('id', $refId)->where('status', 1)->first();
        if (!$ref) {
            return redirect()->back()->withInput()->with('error', 'Data ' . $tipe . ' yang dipilih tidak ditemukan.');
        }
        if ($ref->user_id) {
            return redirect()->back()->withInput()->with('error', 'Data ' . $tipe . ' yang dipilih sudah memiliki akun login.');
        }
        $nama = $tipe === 'guru' ? $ref->nama_guru : $ref->nama_siswa;

        // ── Aturan role sesuai kategori + Ketua Kelas harus eksklusif ──
        if ($tipe === 'siswa') {
            if (!$wantsKetua || count($roleIds) !== 1) {
                return redirect()->back()->withInput()->with('error', 'Akun siswa hanya bisa diberi role Ketua Kelas (tidak boleh digabung role lain).');
            }
        } else { // guru
            if ($wantsKetua) {
                return redirect()->back()->withInput()->with('error', 'Role Ketua Kelas hanya untuk siswa, bukan guru.');
            }
        }
        if ($wantsKetua && count($roleIds) > 1) {
            return redirect()->back()->withInput()->with('error', 'Ketua Kelas tidak boleh digabung dengan role lain untuk mencegah salah akses.');
        }

        // ── Ketua Kelas: validasi kelas & konflik (satu ketua per kelas) ──
        $ketuaResult = null;
        if ($wantsKetua) {
            $ketuaResult = $this->validateKetuaForSiswa($ref, $request->boolean('confirm_change_ketua'));
            if ($ketuaResult['status'] === 'error') {
                return redirect()->back()->withInput()->with('error', $ketuaResult['message']);
            }
            if ($ketuaResult['status'] === 'conflict') {
                $c = $ketuaResult['conflict'];
                return redirect()->back()->withInput()->with('error',
                    'Kelas ' . $c['nama_kelas'] . ' sudah punya Ketua Kelas (' . $c['current_nama'] . '). Centang "Ganti Ketua Kelas" untuk mengganti.');
            }
        }

        // ── Wali Kelas (guru): pilih kelas + validasi konflik (satu wali per kelas) ──
        $waliResult = null;
        if ($wantsWali) {
            $kelasId    = (int) $request->input('wali_kelas_id');
            $waliResult = $this->validateWaliTarget($refId, $kelasId, $request->boolean('confirm_change_wali'));
            if ($waliResult['status'] === 'error') {
                return redirect()->back()->withInput()->with('error', $waliResult['message']);
            }
            if ($waliResult['status'] === 'conflict') {
                $c = $waliResult['conflict'];
                return redirect()->back()->withInput()->with('error',
                    'Kelas ' . $c['nama_kelas'] . ' sudah punya Wali Kelas (' . $c['current_nama'] . '). Centang "Ganti Wali Kelas" untuk mengganti.');
            }
        }

        $actor       = auth()->user();
        $currentUser = $actor->username ?? $actor->nama ?? $actor->email ?? 'system';

        DB::beginTransaction();

        try {
            $user = User::create([
                'nama'          => $nama,
                'username'      => $request->username,
                'email'         => $request->email,
                'password'      => Hash::make($request->password),
                'status'        => 1,
                'user_input'    => $currentUser,
                'tanggal_input' => date('Y-m-d H:i:s'),
            ]);

            // Kaitkan akun ke data guru/siswa.
            DB::table($tipe)->where('id', $refId)->update([
                'user_id'        => $user->id,
                'user_update'    => $currentUser,
                'tanggal_update' => date('Y-m-d H:i:s'),
            ]);

            // Role pivot — Ketua & Wali Kelas TIDAK di pivot (dikelola lewat kelas.*).
            foreach ($roleIds as $roleId) {
                if (($ketuaRoleId && $roleId === $ketuaRoleId) || ($waliRoleId && $roleId === $waliRoleId)) {
                    continue;
                }
                UserRole::create([
                    'user_id'       => $user->id,
                    'role_id'       => $roleId,
                    'status'        => 1,
                    'user_input'    => $currentUser,
                    'tanggal_input' => date('Y-m-d H:i:s'),
                ]);
            }

            // Tetapkan Ketua Kelas (sumber: kelas.ketua_kelas_id).
            if ($wantsKetua && $ketuaResult && $ketuaResult['status'] === 'ok') {
                DB::table('kelas')->where('id', $ketuaResult['kelas']->id)->update([
                    'ketua_kelas_id' => $ref->id,
                    'user_update'    => $currentUser,
                    'tanggal_update' => date('Y-m-d H:i:s'),
                ]);
            }

            // Tetapkan Wali Kelas (sumber: kelas.wali_kelas_id).
            if ($wantsWali && $waliResult && $waliResult['status'] === 'ok') {
                DB::table('kelas')->where('id', $waliResult['kelas']->id)->update([
                    'wali_kelas_id'  => $ref->id,
                    'user_update'    => $currentUser,
                    'tanggal_update' => date('Y-m-d H:i:s'),
                ]);
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', $e->getMessage());
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

        // Ketua Kelas = status turunan dari kelas.ketua_kelas_id, bukan pivot.
        // Selaraskan centang role dengan kenyataan: aktif hanya bila user benar-benar
        // menjadi ketua sebuah kelas; baris pivot Ketua Kelas yang tidak sinkron dibuang.
        $ketuaRoleId = $this->ketuaRoleId();
        $isKetua     = (bool) $User->ketuaKelas();
        if ($ketuaRoleId) {
            $activeRoles = array_values(array_filter($activeRoles, fn ($r) => (int) $r !== $ketuaRoleId));
            if ($isKetua) {
                $activeRoles[] = $ketuaRoleId;
            }
        }

        // Info kelas siswa untuk blok "Ketua Kelas" di form.
        $siswa     = DB::table('siswa')->where('user_id', $User->id)->where('status', 1)->first();
        $ketuaInfo = ['is_siswa' => false, 'has_kelas' => false, 'nama_kelas' => null, 'current_ketua_nama' => null, 'is_current_ketua' => false];
        if ($siswa) {
            $kelas = $siswa->kelas_id
                ? DB::table('kelas')->where('id', $siswa->kelas_id)->where('status', 1)->first()
                : null;
            $ketuaInfo = [
                'is_siswa'           => true,
                'has_kelas'          => (bool) $kelas,
                'nama_kelas'         => $kelas->nama_kelas ?? null,
                'current_ketua_nama' => ($kelas && $kelas->ketua_kelas_id)
                    ? DB::table('siswa')->where('id', $kelas->ketua_kelas_id)->value('nama_siswa')
                    : null,
                'is_current_ketua'   => ($kelas && (int) $kelas->ketua_kelas_id === (int) $siswa->id),
            ];
        }

        // Wali Kelas = turunan dari kelas.wali_kelas_id. Selaraskan centang & sediakan pilihan kelas.
        $waliRoleId       = $this->waliRoleId();
        $guru             = DB::table('guru')->where('user_id', $User->id)->where('status', 1)->first();
        $currentWaliKelas = $guru
            ? DB::table('kelas')->where('wali_kelas_id', $guru->id)->where('status', 1)->first()
            : null;
        if ($waliRoleId) {
            $activeRoles = array_values(array_filter($activeRoles, fn ($r) => (int) $r !== $waliRoleId));
            if ($currentWaliKelas) {
                $activeRoles[] = $waliRoleId;
            }
        }
        $waliInfo = [
            'is_guru'            => (bool) $guru,
            'current_kelas_id'   => $currentWaliKelas->id ?? null,
            'current_kelas_nama' => $currentWaliKelas->nama_kelas ?? null,
        ];
        $kelasList = DB::table('kelas as k')
            ->leftJoin('guru as wg', 'wg.id', '=', 'k.wali_kelas_id')
            ->where('k.status', 1)
            ->orderBy('k.nama_kelas')
            ->get(['k.id', 'k.nama_kelas', 'k.wali_kelas_id', 'wg.nama_guru as current_wali_nama']);

        // Kategori user (tetap; Guru<->Siswa tidak bisa diubah lewat edit) + role yang boleh dipilih.
        // Guru  -> Admin / Petugas Piket / Wali Kelas
        // Siswa -> Ketua Kelas (agar Guru tidak bisa jadi Ketua Kelas, dan sebaliknya)
        // Lainnya (akun non guru/siswa, mis. Admin) -> Admin / Petugas Piket
        $adminRoleId = optional($roles->firstWhere('nama_role', 'Admin'))->id;
        $piketRoleId = optional($roles->firstWhere('nama_role', 'Petugas Piket'))->id;
        if ($ketuaInfo['is_siswa']) {
            $kategori       = 'Siswa';
            $dataTerkait    = $siswa->nama_siswa ?? '-';
            $allowedRoleIds = array_values(array_filter([$ketuaRoleId]));
        } elseif ($waliInfo['is_guru']) {
            $kategori       = 'Guru';
            $dataTerkait    = $guru->nama_guru ?? '-';
            $allowedRoleIds = array_values(array_filter([$adminRoleId, $piketRoleId, $waliRoleId]));
        } else {
            $kategori       = 'Lainnya';
            $dataTerkait    = '-';
            $allowedRoleIds = array_values(array_filter([$adminRoleId, $piketRoleId]));
        }

        return view('UserManagement.edit', compact(
            'User', 'roles', 'activeRoles', 'canEditRole',
            'ketuaRoleId', 'ketuaInfo',
            'waliRoleId', 'waliInfo', 'kelasList',
            'kategori', 'dataTerkait', 'allowedRoleIds'
        ));
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

        // ── Ketua Kelas: validasi & deteksi konflik (satu ketua per kelas) ──
        $ketuaRoleId = $this->ketuaRoleId();
        $roleIds     = array_map('intval', $request->input('role_ids', []));
        $wantsKetua  = $isAdmin && $ketuaRoleId && in_array($ketuaRoleId, $roleIds, true);
        $ketuaResult = null;
        if ($wantsKetua && count($roleIds) > 1) {
            return redirect()->back()->withInput()->with('error', 'Ketua Kelas tidak boleh digabung dengan role lain untuk mencegah salah akses.');
        }
        if ($wantsKetua) {
            $ketuaResult = $this->validateKetuaTarget((int) $id, $request->boolean('confirm_change_ketua'));
            if ($ketuaResult['status'] === 'error') {
                return redirect()->back()->withInput()->with('error', $ketuaResult['message']);
            }
            if ($ketuaResult['status'] === 'conflict') {
                return redirect()->back()->withInput()->with('ketua_conflict', $ketuaResult['conflict']);
            }
        }

        // ── Wali Kelas: validasi & deteksi konflik (satu wali per kelas) ──
        $waliRoleId = $this->waliRoleId();
        $wantsWali  = $isAdmin && $waliRoleId && in_array($waliRoleId, $roleIds, true);
        $waliResult = null;
        if ($wantsWali) {
            $guruRow = DB::table('guru')->where('user_id', $id)->where('status', 1)->first();
            if (!$guruRow) {
                return redirect()->back()->withInput()->with('error', 'User ini belum terhubung ke data guru manapun, jadi tidak bisa dijadikan Wali Kelas.');
            }
            $waliResult = $this->validateWaliTarget((int) $guruRow->id, (int) $request->input('wali_kelas_id'), $request->boolean('confirm_change_wali'));
            if ($waliResult['status'] === 'error') {
                return redirect()->back()->withInput()->with('error', $waliResult['message']);
            }
            if ($waliResult['status'] === 'conflict') {
                return redirect()->back()->withInput()->with('wali_conflict', $waliResult['conflict']);
            }
            $waliResult['guru_id'] = (int) $guruRow->id;
        }

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
                    // Ketua & Wali Kelas dikelola lewat kelas.*, bukan pivot user_role.
                    if (($ketuaRoleId && (int) $roleId === $ketuaRoleId)
                        || ($waliRoleId && (int) $roleId === $waliRoleId)) {
                        continue;
                    }
                    UserRole::create([
                        'user_id'       => (int) $id,
                        'role_id'       => (int) $roleId,
                        'status'        => 1,
                        'user_input'    => $currentUser,
                        'tanggal_input' => date('Y-m-d H:i:s'),
                    ]);
                }

                // Terapkan / lepaskan penetapan Ketua Kelas (sumber: kelas.ketua_kelas_id).
                if ($ketuaRoleId) {
                    $siswa = DB::table('siswa')->where('user_id', $id)->where('status', 1)->first();
                    if ($wantsKetua && $ketuaResult && $ketuaResult['status'] === 'ok') {
                        DB::table('kelas')->where('id', $ketuaResult['kelas']->id)->update([
                            'ketua_kelas_id' => $ketuaResult['siswa']->id,
                            'user_update'    => $currentUser,
                            'tanggal_update' => date('Y-m-d H:i:s'),
                        ]);
                    } elseif (!$wantsKetua && $siswa) {
                        // Ketua Kelas di-uncheck → lepaskan user ini sebagai ketua (jika memang ketua).
                        DB::table('kelas')->where('ketua_kelas_id', $siswa->id)->update([
                            'ketua_kelas_id' => null,
                            'user_update'    => $currentUser,
                            'tanggal_update' => date('Y-m-d H:i:s'),
                        ]);
                    }
                }

                // Terapkan / lepaskan penetapan Wali Kelas (sumber: kelas.wali_kelas_id).
                if ($waliRoleId) {
                    $guruRow = DB::table('guru')->where('user_id', $id)->where('status', 1)->first();
                    if ($wantsWali && $waliResult && $waliResult['status'] === 'ok') {
                        DB::table('kelas')->where('id', $waliResult['kelas']->id)->update([
                            'wali_kelas_id'  => $waliResult['guru_id'],
                            'user_update'    => $currentUser,
                            'tanggal_update' => date('Y-m-d H:i:s'),
                        ]);
                    } elseif (!$wantsWali && $guruRow) {
                        // Wali Kelas di-uncheck → lepaskan guru ini sebagai wali (jika memang wali).
                        DB::table('kelas')->where('wali_kelas_id', $guruRow->id)->update([
                            'wali_kelas_id'  => null,
                            'user_update'    => $currentUser,
                            'tanggal_update' => date('Y-m-d H:i:s'),
                        ]);
                    }
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

    /**
     * ID role "Ketua Kelas" (null bila tidak ada).
     */
    private function ketuaRoleId(): ?int
    {
        $id = Roles::where('nama_role', 'Ketua Kelas')->where('status', 1)->value('id');
        return $id ? (int) $id : null;
    }

    /**
     * ID role "Wali Kelas" (null bila tidak ada).
     */
    private function waliRoleId(): ?int
    {
        $id = Roles::where('nama_role', 'Wali Kelas')->where('status', 1)->value('id');
        return $id ? (int) $id : null;
    }

    /**
     * Validasi target penetapan Wali Kelas (guru → kelas). Tidak menulis ke DB.
     *
     *   ['status' => 'error',    'message'  => string]
     *   ['status' => 'conflict', 'conflict' => array]   // kelas sudah punya wali lain
     *   ['status' => 'ok',       'kelas'    => object]
     */
    private function validateWaliTarget(int $guruId, int $kelasId, bool $confirm): array
    {
        if (!$kelasId) {
            return ['status' => 'error', 'message' => 'Pilih kelas yang akan diampu sebagai Wali Kelas.'];
        }
        $kelas = DB::table('kelas')->where('id', $kelasId)->where('status', 1)->first();
        if (!$kelas) {
            return ['status' => 'error', 'message' => 'Kelas yang dipilih tidak ditemukan atau sudah tidak aktif.'];
        }
        if ($kelas->wali_kelas_id
            && (int) $kelas->wali_kelas_id !== $guruId
            && !$confirm) {
            $currentNama = DB::table('guru')->where('id', $kelas->wali_kelas_id)->value('nama_guru');
            return ['status' => 'conflict', 'conflict' => [
                'kelas_id'     => $kelas->id,
                'nama_kelas'   => $kelas->nama_kelas,
                'current_nama' => $currentNama ?: '-',
            ]];
        }
        return ['status' => 'ok', 'kelas' => $kelas];
    }

    /**
     * Validasi target penetapan Ketua Kelas untuk sebuah user. Tidak menulis ke DB.
     *
     * Return salah satu:
     *   ['status' => 'error',    'message'  => string]
     *   ['status' => 'conflict', 'conflict' => array]   // kelas sudah punya ketua lain
     *   ['status' => 'ok',       'siswa'    => object, 'kelas' => object]
     */
    private function validateKetuaTarget(int $userId, bool $confirm): array
    {
        $siswa = DB::table('siswa')->where('user_id', $userId)->where('status', 1)->first();
        if (!$siswa) {
            return ['status' => 'error', 'message' => 'User ini belum terhubung ke data siswa manapun, jadi tidak bisa dijadikan Ketua Kelas.'];
        }
        return $this->validateKetuaForSiswa($siswa, $confirm);
    }

    /**
     * Validasi kelas & konflik ketua untuk sebuah record siswa (tanpa menulis ke DB).
     */
    private function validateKetuaForSiswa(object $siswa, bool $confirm): array
    {
        if (!$siswa->kelas_id) {
            return ['status' => 'error', 'message' => 'Siswa ini belum terdaftar di kelas manapun. Tetapkan kelas siswa terlebih dahulu.'];
        }
        $kelas = DB::table('kelas')->where('id', $siswa->kelas_id)->where('status', 1)->first();
        if (!$kelas) {
            return ['status' => 'error', 'message' => 'Kelas siswa ini tidak ditemukan atau sudah tidak aktif.'];
        }
        if ($kelas->ketua_kelas_id
            && (int) $kelas->ketua_kelas_id !== (int) $siswa->id
            && !$confirm) {
            $currentNama = DB::table('siswa')->where('id', $kelas->ketua_kelas_id)->value('nama_siswa');
            return ['status' => 'conflict', 'conflict' => [
                'kelas_id'     => $kelas->id,
                'nama_kelas'   => $kelas->nama_kelas,
                'current_nama' => $currentNama ?: '-',
                'new_nama'     => $siswa->nama_siswa,
            ]];
        }
        return ['status' => 'ok', 'siswa' => $siswa, 'kelas' => $kelas];
    }
}