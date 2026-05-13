<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PersonalityScore extends Model
{
    protected $fillable = ['user_id', 'category_id', 'score'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(AssessmentCategory::class, 'category_id');
    }
}
