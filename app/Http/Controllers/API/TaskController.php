<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Task;

class TaskController extends Controller
{
    public function store(Request $request)
    {
        // $request->validate([
        //     'title' => 'required|string|max:50',
        //     'description' => 'required|string',
        //     'priority' => 'required|string',
        //     'reporter_id' => 'required|integer|exists:users,id',
        //     'assignee_id' => 'required|integer|exists:users,id',
        //     'due_date' => ['required', (new Date)->format('Y-m-d')->afterToday()]
        // ]);

        return Task::create($request->only([
            'title',
            'description',
            'status_id',
            'priority',
            'reporter_id',
            'assignee_id',
            'due_date'
        ]));
    }
}
