<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $guarded = [];

    protected $casts = ['data' => 'array'];

    protected function scopePublished($query)
    {
        return $query->where('published', true);
    }
}
