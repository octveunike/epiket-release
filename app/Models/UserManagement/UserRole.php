<?php

namespace App\Models\UserManagement;

use Illuminate\Database\Eloquent\Model;

class UserRole extends Model
{
    protected $table = 'user_role';
    public $timestamps = false;
    protected $guarded = [];
}