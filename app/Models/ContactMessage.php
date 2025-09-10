<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'company',
        'subject',
        'message',
        'service_interest',
        'budget_range',
        'preferred_contact',
        'status',
        'admin_notes',
        'ip_address',
        'user_agent',
        'replied_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'replied_at' => 'datetime',
    ];

    public const UPDATED_AT = null;

    // Status constants
    public const STATUS_NEW = 'new';
    public const STATUS_READ = 'read';
    public const STATUS_REPLIED = 'replied';
    public const STATUS_CLOSED = 'closed';

    // Preferred contact constants
    public const CONTACT_EMAIL = 'email';
    public const CONTACT_PHONE = 'phone';
    public const CONTACT_BOTH = 'both';

    public static function getStatuses(): array
    {
        return [
            self::STATUS_NEW => 'New',
            self::STATUS_READ => 'Read',
            self::STATUS_REPLIED => 'Replied',
            self::STATUS_CLOSED => 'Closed',
        ];
    }

    public static function getContactMethods(): array
    {
        return [
            self::CONTACT_EMAIL => 'Email',
            self::CONTACT_PHONE => 'Phone',
            self::CONTACT_BOTH => 'Both',
        ];
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeNew($query)
    {
        return $query->where('status', self::STATUS_NEW);
    }

    public function scopeRead($query)
    {
        return $query->where('status', self::STATUS_READ);
    }

    public function scopeReplied($query)
    {
        return $query->where('status', self::STATUS_REPLIED);
    }

    public function scopeClosed($query)
    {
        return $query->where('status', self::STATUS_CLOSED);
    }

    public function scopeByService($query, $service)
    {
        return $query->where('service_interest', 'LIKE', "%{$service}%");
    }

    public function scopeByBudget($query, $budget)
    {
        return $query->where('budget_range', $budget);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'LIKE', "%{$search}%")
              ->orWhere('email', 'LIKE', "%{$search}%")
              ->orWhere('company', 'LIKE', "%{$search}%")
              ->orWhere('subject', 'LIKE', "%{$search}%")
              ->orWhere('message', 'LIKE', "%{$search}%");
        });
    }

    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    public function getStatusLabelAttribute()
    {
        return self::getStatuses()[$this->status] ?? $this->status;
    }

    public function getPreferredContactLabelAttribute()
    {
        return self::getContactMethods()[$this->preferred_contact] ?? $this->preferred_contact;
    }

    public function getMessagePreviewAttribute()
    {
        return strlen($this->message) > 150 
            ? substr($this->message, 0, 150) . '...' 
            : $this->message;
    }

    public function getIsNewAttribute()
    {
        return $this->status === self::STATUS_NEW;
    }

    public function getIsRepliedAttribute()
    {
        return $this->status === self::STATUS_REPLIED;
    }

    public function getIsClosedAttribute()
    {
        return $this->status === self::STATUS_CLOSED;
    }

    public function getResponseTimeAttribute()
    {
        if ($this->replied_at) {
            return $this->created_at->diffInHours($this->replied_at);
        }
        return null;
    }

    public function getTimeAgoAttribute()
    {
        return $this->created_at->diffForHumans();
    }
}