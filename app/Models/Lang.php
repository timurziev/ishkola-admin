<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Auth\User\User;

class Lang extends Model
{
    protected $fillable = ['name', 'image', 'basic_price', 'pro_price', 'indiv_price_60', 'indiv_price_45'];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
