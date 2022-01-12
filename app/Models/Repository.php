<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Repository extends Model 
{
    use HasFactory;

    public $fillable = [
        'name',
        'slug',
        'url',
        'is_public',
        'public_key',
        'private_key',
        'last_released_at',
        'last_released_version'
    ];

    public $casts = [
        'last_releasd_at' => 'datetime',
    ];
}
