<?php

namespace App\Models\Apps;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PeriodeAkademik extends Model
{
    use HasFactory;
    protected $table = 'periode_akademik';
    public $incrementing = true;
    protected $guarded = [];
    public $timestamps = false;
}
