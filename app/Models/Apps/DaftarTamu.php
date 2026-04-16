<?php

namespace App\Models\Apps;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DaftarTamu extends Model
{
    use HasFactory;

    protected $table = 'daftar_tamu';
    public $incrementing = true;
    protected $guarded = [];
    public $timestamps = false;
}