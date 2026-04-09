<?php

namespace App\Models\Apps;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Admin\Guru;
use App\Models\Admin\Siswa;
use App\Models\Apps\PeriodeAkademik;

class Kelas extends Model
{
    use HasFactory;

    protected $table = 'kelas';
    public $incrementing = true;
    public $timestamps = false;
    protected $guarded = [];

    public function waliKelas()
    {
        return $this->belongsTo(Guru::class, 'wali_kelas_id', 'id');
    }

    public function ketuaKelas()
    {
        return $this->belongsTo(Siswa::class, 'ketua_kelas_id', 'id');
    }

    public function periodeAkademik()
    {
        return $this->belongsTo(PeriodeAkademik::class, 'periode_akademik_id', 'id');
    }

    public function siswa()
    {
        return $this->hasMany(Siswa::class, 'kelas_id', 'id');
    }
}