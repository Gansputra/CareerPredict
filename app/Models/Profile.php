<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $fillable = [
        'user_id', 'phone', 'address', 'bio', 'avatar', 'cv_path', 'cv_career_category', 'cv_career_confidence', 'education', 'experience'
    ];

    protected $casts = [
        'education' => 'array',
        'experience' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
