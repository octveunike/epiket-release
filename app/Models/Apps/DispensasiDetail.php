<?php

namespace App\Models\Apps;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Admin\Siswa;
use App\Models\Apps\Dispensasi;

class DispensasiDetail extends Model
{
    use HasFactory;

    protected $table = 'dispensasi_detail';
    public $incrementing = true;
    protected $guarded = [];
    public $timestamps = false;

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    public function dispensasi()
    {
        return $this->belongsTo(Dispensasi::class, 'dispensasi_id');
    }
}