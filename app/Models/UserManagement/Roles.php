<?php

namespace App\Models\UserManagement;

use Illuminate\Database\Eloquent\Model;

class Roles extends Model
{
    protected $table = 'roles';
    public $timestamps = false;
    protected $guarded = [];

    public function users()
    {
        return $this->belongsToMany(
            \App\Models\User::class,
            'user_role',
            'role_id',
            'user_id'
        )->wherePivot('status', '1');
    }
}