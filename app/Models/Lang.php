<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Auth\User\User;

class Lang extends Model
{
    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}