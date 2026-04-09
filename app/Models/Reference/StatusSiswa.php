<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatusSiswa extends Model
{
    use HasFactory;
    protected $table = 'status_siswa';
    public $incrementing = true;
    protected $guarded = [];
    public $timestamps = false;
}
