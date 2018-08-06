<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Auth\User\User;

class Discount extends Model
{
    protected $fillable = ['user_id', 'lang_name', 'amount'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
