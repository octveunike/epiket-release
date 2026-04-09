<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guru extends Model
{
    use HasFactory;

    protected $table = 'guru';
    public $incrementing = true;
    protected $guarded = [];
    public $timestamps = false;

    /**
     * Guru bisa jadi wali kelas
     */
    public function kelas()
    {
        return $this->hasMany(Kelas::class, 'wali_kelas_id');
    }
}