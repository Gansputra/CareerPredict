<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SponsorBanner extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'image_path',
        'link_url',
        'cta_text',
        'is_active',
        'order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
    ];
}
