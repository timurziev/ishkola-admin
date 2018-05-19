<?php

namespace App\Models\Auth\User;

use Illuminate\Database\Eloquent\Model;

class Avatar extends Model
{
    protected $fillable = ['name', 'user_id'];
}
