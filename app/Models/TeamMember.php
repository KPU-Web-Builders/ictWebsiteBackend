<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeamMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'role',
        'bio',
        'photo_url',
        'is_verified',
        'skills',
        'linkedin_url',
        'github_url',
        'twitter_url',
        'email',
        'phone',
        'sort_order',
        'is_active',
        'joined_date',
    ];

    protected $casts = [
        'skills' => 'array',
        'is_verified' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'joined_date' => 'date',
        'created_at' => 'datetime',
    ];

    public const UPDATED_AT = null;

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    public function scopeByRole($query, $role)
    {
        return $query->where('role', 'LIKE', "%{$role}%");
    }

    public function getSkillsStringAttribute()
    {
        if (is_array($this->skills)) {
            return implode(', ', $this->skills);
        }
        return '';
    }

    public function getSkillsCountAttribute()
    {
        if (is_array($this->skills)) {
            return count($this->skills);
        }
        return 0;
    }

    public function getYearsOfExperienceAttribute()
    {
        if ($this->joined_date) {
            return now()->diffInYears($this->joined_date);
        }
        return null;
    }

    public function getSocialLinksAttribute()
    {
        return [
            'linkedin' => $this->linkedin_url,
            'github' => $this->github_url,
            'twitter' => $this->twitter_url,
        ];
    }

    public function getHasSocialLinksAttribute()
    {
        return !empty($this->linkedin_url) || !empty($this->github_url) || !empty($this->twitter_url);
    }
}