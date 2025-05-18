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

    public function reporter()
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }

    public function status()
    {
        return $this->hasOne(Status::class);
    }
}
