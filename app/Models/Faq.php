<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Faq extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'question',
        'answer',
        'is_featured',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'category_id' => 'integer',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(FaqCategory::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('created_at');
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('question', 'LIKE', "%{$search}%")
              ->orWhere('answer', 'LIKE', "%{$search}%");
        });
    }

    public function getQuestionPreviewAttribute()
    {
        return strlen($this->question) > 100 
            ? substr($this->question, 0, 100) . '...' 
            : $this->question;
    }

    public function getAnswerPreviewAttribute()
    {
        return strlen($this->answer) > 200 
            ? substr($this->answer, 0, 200) . '...' 
            : $this->answer;
    }

    public function getAnswerWordCountAttribute()
    {
        return str_word_count(strip_tags($this->answer));
    }

    public function getQuestionWordCountAttribute()
    {
        return str_word_count($this->question);
    }
}