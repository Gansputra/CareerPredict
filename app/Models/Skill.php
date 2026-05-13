<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    protected $fillable = ['name', 'type'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_skills')->withPivot('level')->withTimestamps();
    }
}
