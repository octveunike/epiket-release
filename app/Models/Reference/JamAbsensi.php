<?php

namespace App\Models\Reference;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JamAbsensi extends Model
{
    use HasFactory;
    protected $table = 'jam_absensi';
    public $incrementing = true;
    protected $guarded = [];
    public $timestamps = false;
}
