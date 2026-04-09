<?php

namespace App\Models\Apps;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Reference\JamAbsensi;

class AbsensiDetailJam extends Model
{
    use HasFactory;

    protected $table = 'absensi_detail_jam';
    public $timestamps = false;
    protected $guarded = [];

    public function detail()
    {
        return $this->belongsTo(AbsensiDetail::class, 'absensi_detail_id', 'id');
    }

    public function jam()
    {
        return $this->belongsTo(JamAbsensi::class, 'jam_ke_id', 'id');
    }
}