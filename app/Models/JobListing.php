<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobListing extends Model
{
    protected $fillable = [
        'category_id', 'title', 'slug', 'description', 'requirements',
        'location', 'salary_range', 'type', 'company_name', 'company_logo', 'url', 'is_active', 'is_dummy'
    ];

    public function category()
    {
        return $this->belongsTo(JobCategory::class, 'category_id');
    }

    public function applications()
    {
        return $this->hasMany(Application::class, 'job_id');
    }

    public function recommendations()
    {
        return $this->hasMany(Recommendation::class, 'job_id');
    }

    public function scopeActive($query)
    {
        $query->where('is_active', true);
        if (env('HIDE_DUMMY_JOBS', app()->environment('production'))) {
            $query->where('is_dummy', false);
        }
        return $query;
    }
}
