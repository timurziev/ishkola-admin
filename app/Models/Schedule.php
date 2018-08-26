<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Auth\User\User;

class Schedule extends Model
{
    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'schedule'
    ];

    protected $fillable = ['lesson_id', 'schedule', 'comment', 'meid'];

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
