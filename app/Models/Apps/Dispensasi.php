<?php

namespace App\Models\Apps;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Apps\Organisasi;
use App\Models\Apps\PeriodeAkademik;
use App\Models\Reference\StatusValidasi;

class Dispensasi extends Model
{
    use HasFactory;

    protected $table = 'dispensasi';
    public $incrementing = true;
    protected $guarded = [];
    public $timestamps = false;

    public function organisasi()
    {
        return $this->belongsTo(Organisasi::class, 'organisasi_id');
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
        return $this->hasMany(DispensasiDetail::class, 'dispensasi_id')->where('status', 1);
    }
}