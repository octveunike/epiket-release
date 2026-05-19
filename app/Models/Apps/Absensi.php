<?php

namespace App\Models\Apps;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Apps\Kelas;
use App\Models\Apps\PeriodeAkademik;
use App\Models\Reference\StatusValidasi;
use App\Models\User;

class Absensi extends Model
{
    use HasFactory;

    protected $table = 'absensi';

    protected $fillable = [
        'kelas_id',
        'tanggal',
        'status_validasi_id',
        'periode_akademik_id',
        'status',
        'user_input',
        'tanggal_input',
        'user_update',
        'tanggal_update',
    ];

    public $timestamps = false;

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    public function periodeAkademik()
    {
        return $this->belongsTo(PeriodeAkademik::class, 'periode_akademik_id');
    }

    public function statusValidasi()
    {
        return $this->belongsTo(StatusValidasi::class, 'status_validasi_id');
    }

    public function details()
    {
        return $this->hasMany(AbsensiDetail::class, 'absensi_id')->where('status', 1);
    }

    public function userInput()
    {
        return $this->belongsTo(User::class, 'user_input', 'id');
    }

    public function userUpdate()
    {
        return $this->belongsTo(User::class, 'user_update', 'id');
    }
}