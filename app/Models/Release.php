<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Release extends Model
{
    use HasFactory;

    public $fillable = [
        'version',
        'hash',
        'notes',
        'released_at',
    ];

    public function repository()
    {
        return $this->belongsTo(Repository::class);
    }
}
