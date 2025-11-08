<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TypeOfHosting extends Model
{
    protected $fillable = [
        'name',
        'image',
        'description',
    ];
}
