<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssessmentQuestion extends Model
{
    protected $fillable = ['category_id', 'question', 'weight'];

    public function category()
    {
        return $this->belongsTo(AssessmentCategory::class, 'category_id');
    }
}
