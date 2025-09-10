<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Portfolio extends Model
{
    use HasFactory;

    protected $table = 'portfolio';

    protected $fillable = [
        'title',
        'slug',
        'description',
        'client_name',
        'project_url',
        'featured_image',
        'gallery_images',
        'service_id',
        'technologies_used',
        'project_date',
        'is_featured',
        'is_published',
    ];

    protected $casts = [
        'gallery_images' => 'array',
        'technologies_used' => 'array',
        'project_date' => 'date',
        'is_featured' => 'boolean',
        'is_published' => 'boolean',
        'service_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($portfolio) {
            if (empty($portfolio->slug)) {
                $portfolio->slug = Str::slug($portfolio->title);
            }
        });

        static::updating(function ($portfolio) {
            if ($portfolio->isDirty('title') && empty($portfolio->slug)) {
                $portfolio->slug = Str::slug($portfolio->title);
            }
        });
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeByService($query, $serviceId)
    {
        return $query->where('service_id', $serviceId);
    }

    public function scopeRecent($query, $limit = 10)
    {
        return $query->orderBy('project_date', 'desc')->limit($limit);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('project_date', 'desc')->orderBy('created_at', 'desc');
    }

    public function getTechnologiesStringAttribute()
    {
        if (is_array($this->technologies_used)) {
            return implode(', ', $this->technologies_used);
        }
        return '';
    }

    public function getGalleryImagesCountAttribute()
    {
        if (is_array($this->gallery_images)) {
            return count($this->gallery_images);
        }
        return 0;
    }
}