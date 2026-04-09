<?php

namespace App\Models\Apps;

use Illuminate\Database\Eloquent\Model;
use App\Models\Apps\Absensi;
use App\Models\Apps\AbsensiDetail;
use App\Models\Admin\Siswa;
use App\Models\Apps\PeriodeAkademik;

class Keterlambatan extends Model
{
    protected $table = 'keterlambatan';

    protected $fillable = [
        'absensi_id',
        'siswa_id',
        'waktu_masuk',
        'alasan',
        'periode_akademik_id',
        'status',
        'user_input',
        'tanggal_input',
        'user_update',
        'tanggal_update',
    ];

    public function absensi()
    {
        return $this->belongsTo(Absensi::class, 'absensi_id');
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    public function periodeAkademik()
    {
        return $this->belongsTo(PeriodeAkademik::class, 'periode_akademik_id');
    }
}