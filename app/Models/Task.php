<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'priority',
        'status_id',
        'reporter_id',
        'assignee_id',
        'due_date'
    ];

    protected static function booted()
    {
        static::creating(function ($task) {
            $count = $task->count();
            $task->key = 'TASK-' . ++$count;
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
