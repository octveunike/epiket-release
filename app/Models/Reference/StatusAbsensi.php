<?php

namespace App\Models\Reference;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatusAbsensi extends Model
{
    use HasFactory;
    protected $table = 'status_absensi';
    public $incrementing = true;
    protected $guarded = [];
    public $timestamps = false;
}
