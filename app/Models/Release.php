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
    ];

    public function repository()
    {
        return $this->belongsTo(Repository::class);
    }
}
