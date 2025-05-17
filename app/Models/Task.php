<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'title',
        'description',
        'priority',
        'status_id',
        'reporter_id',
        'assignee_id',
        'due_date',
        'resolution_date'
    ];

    protected static function booted()
    {
        static::creating(function ($task) {
            $task->key = 'TASK-' . nextSequence('task');
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function status()
    {
        return $this->hasOne(Status::class);
    }
}
