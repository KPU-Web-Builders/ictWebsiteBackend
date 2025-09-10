<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Testimonial extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_name',
        'company',
        'position',
        'testimonial',
        'rating',
        'photo_url',
        'service_id',
        'is_featured',
        'is_approved',
        'sort_order',
    ];

    protected $casts = [
        'rating' => 'integer',
        'service_id' => 'integer',
        'is_featured' => 'boolean',
        'is_approved' => 'boolean',
        'sort_order' => 'integer',
        'created_at' => 'datetime',
    ];

    public const UPDATED_AT = null;

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeByService($query, $serviceId)
    {
        return $query->where('service_id', $serviceId);
    }

    public function scopeByRating($query, $rating)
    {
        return $query->where('rating', $rating);
    }

    public function scopeMinRating($query, $minRating)
    {
        return $query->where('rating', '>=', $minRating);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('created_at', 'desc');
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('client_name', 'LIKE', "%{$search}%")
              ->orWhere('company', 'LIKE', "%{$search}%")
              ->orWhere('position', 'LIKE', "%{$search}%")
              ->orWhere('testimonial', 'LIKE', "%{$search}%");
        });
    }

    public function getTestimonialPreviewAttribute()
    {
        return strlen($this->testimonial) > 150 
            ? substr($this->testimonial, 0, 150) . '...' 
            : $this->testimonial;
    }

    public function getStarRatingAttribute()
    {
        return str_repeat('★', $this->rating) . str_repeat('☆', 5 - $this->rating);
    }

    public function getRatingPercentageAttribute()
    {
        return ($this->rating / 5) * 100;
    }

    public function getClientFullNameAttribute()
    {
        $parts = [$this->client_name];
        
        if ($this->position && $this->company) {
            $parts[] = $this->position . ' at ' . $this->company;
        } elseif ($this->position) {
            $parts[] = $this->position;
        } elseif ($this->company) {
            $parts[] = $this->company;
        }

        return implode(', ', $parts);
    }

    public function getWordCountAttribute()
    {
        return str_word_count($this->testimonial);
    }

    public function getHasPhotoAttribute()
    {
        return !empty($this->photo_url);
    }

    public static function getAverageRating()
    {
        return static::approved()->avg('rating') ?: 0;
    }

    public static function getRatingDistribution()
    {
        return static::approved()
            ->selectRaw('rating, COUNT(*) as count')
            ->groupBy('rating')
            ->orderBy('rating', 'desc')
            ->get()
            ->pluck('count', 'rating')
            ->toArray();
    }
}