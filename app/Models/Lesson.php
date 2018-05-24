<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Auth\User\User;

class Lesson extends Model
{
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
