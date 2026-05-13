<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecommendationHistory extends Model
{
    protected $fillable = ['user_id', 'results_data'];

    protected $casts = [
        'results_data' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
