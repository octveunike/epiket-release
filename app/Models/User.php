<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\UserManagement\Roles;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'users';
    public $timestamps = false;

    protected $with = ['roles'];

    protected $fillable = [
        'nama',
        'username',
        'email',
        'password',
        'status',
        'user_input',
        'tanggal_input',
        'user_update',
        'tanggal_update',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // 'password' => 'hashed' DIHAPUS
    // Kalau ini aktif + controller pakai Hash::make() = password di-hash 2x = tidak bisa login
    // Hashing dilakukan manual di UserManagementController dengan Hash::make()
    protected $casts = [];

    /**
     * Auth pakai username, bukan email
     */
    public function getAuthIdentifierName()
    {
        return 'username';
    }

    /**
     * Relasi ke roles (pivot user_role)
     * Hanya role aktif (status = 1)
     */
    public function roles()
    {
        return $this->belongsToMany(
            Roles::class,
            'user_role',
            'user_id',
            'role_id'
        )->wherePivot('status', 1);
    }

    /**
     * Cek apakah user punya role tertentu.
     *
     * "Ketua Kelas" dan "Wali Kelas" di-derive dari data:
     *   - Ketua Kelas ⇔ ada kelas aktif dengan ketua_kelas_id → siswa → siswa.user_id = user.id
     *   - Wali Kelas  ⇔ ada kelas aktif dengan wali_kelas_id → guru → guru.user_id = user.id
     * Dua role ini TIDAK dibaca dari pivot user_role supaya tidak ada drift.
     *
     * Role lain tetap dibaca dari user_role (status=1).
     */
    public function hasRole(string|array $roleName): bool
    {
        $roles = is_array($roleName) ? $roleName : [$roleName];

        if (in_array('Ketua Kelas', $roles, true) && $this->ketuaKelas()) {
            return true;
        }
        if (in_array('Wali Kelas', $roles, true) && $this->waliKelas()) {
            return true;
        }

        return $this->roles->whereIn('nama_role', $roles)->isNotEmpty();
    }

    public function siswa()
    {
        return $this->hasOne(\App\Models\Admin\Siswa::class, 'user_id', 'id');
    }

    public function guru()
    {
        return $this->hasOne(\App\Models\Admin\Guru::class, 'user_id', 'id');
    }

    protected ?object $ketuaKelasCache = null;
    protected bool $ketuaKelasResolved = false;

    public function ketuaKelas()
    {
        if (!$this->ketuaKelasResolved) {
            $this->ketuaKelasCache = DB::table('kelas')
                ->join('siswa as s', 's.id', '=', 'kelas.ketua_kelas_id')
                ->where('kelas.status', 1)
                ->where('s.status', 1)
                ->where('s.user_id', $this->id)
                ->select('kelas.id', 'kelas.nama_kelas')
                ->first();
            $this->ketuaKelasResolved = true;
        }

        return $this->ketuaKelasCache;
    }

    public function ketuaKelasId(): ?int
    {
        return optional($this->ketuaKelas())->id;
    }

    protected ?object $waliKelasCache = null;
    protected bool $waliKelasResolved = false;

    public function waliKelas()
    {
        if (!$this->waliKelasResolved) {
            $this->waliKelasCache = DB::table('kelas')
                ->join('guru as g', 'g.id', '=', 'kelas.wali_kelas_id')
                ->where('kelas.status', 1)
                ->where('g.status', 1)
                ->where('g.user_id', $this->id)
                ->select('kelas.id', 'kelas.nama_kelas')
                ->first();
            $this->waliKelasResolved = true;
        }

        return $this->waliKelasCache;
    }

    public function waliKelasId(): ?int
    {
        return optional($this->waliKelas())->id;
    }
}