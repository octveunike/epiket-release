<?php

namespace App\Models\Apps;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Admin\Siswa;
use App\Models\Reference\StatusAbsensi;

class AbsensiDetail extends Model
{
    use HasFactory;

    protected $table = 'absensi_detail';
    public $timestamps = false;
    protected $guarded = [];

    public function absensi()
    {
        return $this->belongsTo(Absensi::class, 'absensi_id', 'id');
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id', 'id');
    }

    public function statusAbsensi()
    {
        return $this->belongsTo(StatusAbsensi::class, 'status_absensi_id', 'id');
    }

    public function jams()
    {
        return $this->hasMany(AbsensiDetailJam::class, 'absensi_detail_id', 'id')->where('status', 1);
    }
}