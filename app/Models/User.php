<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    public function admin()
    {
        return $this->hasOne(Admin::class);
    }

    public function skills()
    {
        return $this->belongsToMany(Skill::class, 'user_skills')->withPivot('level')->withTimestamps();
    }

    public function interests()
    {
        return $this->belongsToMany(Interest::class, 'user_interests')->withTimestamps();
    }

    public function recommendations()
    {
        return $this->hasMany(Recommendation::class);
    }

    public function recommendationHistories()
    {
        return $this->hasMany(RecommendationHistory::class);
    }

    public function applications()
    {
        return $this->hasMany(Application::class);
    }

    public function personalityScores()
    {
        return $this->hasMany(PersonalityScore::class);
    }

    public function assessmentAnswers()
    {
        return $this->hasMany(UserAssessmentAnswer::class);
    }
}
