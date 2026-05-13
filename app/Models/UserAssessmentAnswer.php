<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAssessmentAnswer extends Model
{
    protected $fillable = ['user_id', 'question_id', 'score'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function question()
    {
        return $this->belongsTo(AssessmentQuestion::class);
    }
}
