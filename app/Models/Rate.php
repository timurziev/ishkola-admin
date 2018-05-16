<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Auth\User\User;

class Rate extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
