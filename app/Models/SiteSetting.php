<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'setting_key',
        'setting_value',
        'setting_type',
        'description',
    ];

    protected $casts = [
        'setting_value' => 'string',
        'setting_type' => 'string',
        'updated_at' => 'datetime',
    ];

    public $timestamps = false;

    protected $dates = ['updated_at'];

    public function getValueAttribute()
    {
        switch ($this->setting_type) {
            case 'json':
                return json_decode($this->setting_value, true);
            case 'boolean':
                return (bool) $this->setting_value;
            default:
                return $this->setting_value;
        }
    }

    public function setValueAttribute($value)
    {
        if ($this->setting_type === 'json') {
            $this->attributes['setting_value'] = json_encode($value);
        } elseif ($this->setting_type === 'boolean') {
            $this->attributes['setting_value'] = $value ? '1' : '0';
        } else {
            $this->attributes['setting_value'] = $value;
        }
    }
}