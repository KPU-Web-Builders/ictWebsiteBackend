<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class services_card extends Model
{
    use HasFactory;

    protected $table = 'services_cards';

    protected $fillable = [
        'name',
        'description',
        'picture',
    ];
}
