<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Auth\User\User;

class Group extends Model
{
    protected $fillable = ['name', 'lang_id'];
    protected $appends = ['userList'];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function getUserListAttribute()
    {
        return $this->users->pluck('id')->toArray();
    }

    public function lang()
    {
        return $this->belongsTo(Lang::class);
    }
}
