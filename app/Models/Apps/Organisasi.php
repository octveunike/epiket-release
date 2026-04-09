<?php

namespace App\Models\Apps;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Admin\Guru;
use App\Models\Apps\SiswaOrganisasi;

class Organisasi extends Model
{
    use HasFactory;

    protected $table = 'organisasi';
    public $incrementing = true;
    public $timestamps = false;
    protected $guarded = [];

    public function pembina()
    {
        return $this->belongsTo(Guru::class, 'pembina_id', 'id');
    }

    public function siswaOrganisasi()
    {
        return $this->hasMany(SiswaOrganisasi::class, 'organisasi_id', 'id')
                    ->where('status', 1);
    }
}