<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssessmentCategory extends Model
{
    protected $fillable = ['name', 'slug', 'description', 'icon'];

    public function questions()
    {
        return $this->hasMany(AssessmentQuestion::class, 'category_id');
    }
}
