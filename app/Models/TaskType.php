<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TaskType extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name',
        'title',
        'description',
        'points_reward',
        'verification_method',
        'requirements',
        'is_active'
    ];

    protected $casts = [
        'requirements' => 'array',
        'is_active' => 'boolean',
    ];
    
    public function userTasks(): HasMany
    {
        return $this->hasMany(UserTask::class, 'task_type_id');
    }
}