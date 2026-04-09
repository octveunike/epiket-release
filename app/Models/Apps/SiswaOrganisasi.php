<?php

namespace App\Models\Apps;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Admin\Siswa;

class SiswaOrganisasi extends Model
{
    use HasFactory;

    protected $table      = 'siswa_organisasi';
    public $incrementing  = true;
    public $timestamps    = false;
    protected $guarded    = [];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id', 'id');
    }
}