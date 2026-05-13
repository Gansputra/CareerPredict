<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobCategory extends Model
{
    protected $fillable = ['name', 'slug', 'description'];

    public function jobs()
    {
        return $this->hasMany(JobListing::class, 'category_id');
    }
}
