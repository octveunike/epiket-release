<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Staff extends Model
{
    use HasFactory;

    protected $table = 'staff';
    public $incrementing = true;
    protected $guarded = [];
    public $timestamps = false;

    /**
     * relasi ke user
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}