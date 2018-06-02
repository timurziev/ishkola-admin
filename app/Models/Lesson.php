<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Auth\User\User;

class Lesson extends Model
{
    protected $fillable = ['lang_id', 'group_id', 'format', 'duration', 'price', 'quantity'];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    public function lang()
    {
        return $this->belongsTo(Lang::class);
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function getUserListAttribute()
    {
        return $this->users->pluck('id')->toArray();
    }
}
