<?php

namespace App\Models\Reference;

use Illuminate\Database\Eloquent\Model;

class StatusValidasi extends Model
{
    protected $table = 'status_validasi';

    protected $fillable = [
        'nama_status', 'keterangan', 'status',
        'user_input', 'tanggal_input', 'user_update', 'tanggal_update',
    ];

    public $timestamps = false;
}