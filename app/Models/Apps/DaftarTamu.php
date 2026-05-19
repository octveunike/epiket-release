<?php

namespace App\Models\Apps;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class DaftarTamu extends Model
{
    use HasFactory;

    protected $table = 'daftar_tamu';
    public $incrementing = true;
    protected $guarded = [];
    public $timestamps = false;

    public function userInput()
    {
        return $this->belongsTo(User::class, 'user_input', 'id');
    }

    public function userUpdate()
    {
        return $this->belongsTo(User::class, 'user_update', 'id');
    }
}