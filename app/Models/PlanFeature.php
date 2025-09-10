<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlanFeature extends Model
{
    use HasFactory;

    protected $fillable = [
        'plan_id',
        'feature_name',
        'is_included',
        'feature_value',
        'tooltip',
        'sort_order',
    ];

    protected $casts = [
        'plan_id' => 'integer',
        'is_included' => 'boolean',
        'sort_order' => 'integer',
    ];

    public $timestamps = false;

    public function plan(): BelongsTo
    {
        return $this->belongsTo(HostingPlan::class);
    }

    public function scopeIncluded($query)
    {
        return $query->where('is_included', true);
    }

    public function scopeNotIncluded($query)
    {
        return $query->where('is_included', false);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('feature_name');
    }

    public function scopeByPlan($query, $planId)
    {
        return $query->where('plan_id', $planId);
    }

    public function getFormattedFeatureAttribute()
    {
        if ($this->feature_value) {
            return $this->feature_name . ': ' . $this->feature_value;
        }
        return $this->feature_name;
    }
}