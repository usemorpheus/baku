<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = ['data' => 'array'];

    protected function scopePublished($query)
    {
        return $query->where('published', true);
    }
}
