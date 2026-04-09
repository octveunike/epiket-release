<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\UserManagement\Roles;

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
     * Bisa cek satu role (string) atau beberapa sekaligus (array).
     * Contoh: hasRole('admin') atau hasRole(['admin', 'wali_kelas'])
     */
    public function hasRole(string|array $roleName): bool
    {
        $roles = is_array($roleName) ? $roleName : [$roleName];
        return $this->roles->whereIn('nama_role', $roles)->isNotEmpty();
    }
}