<?php

namespace App\Http\Controllers\API;

use App\Enums\TaskPriority;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Status;
use App\Models\Task;
use Illuminate\Validation\Rule;


class TaskController extends Controller
{
    const INITIAL_STATUS = 'Backlog';

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:50',
            'description' => 'required|string',
            'priority' => [Rule::enum(TaskPriority::class)],
            'reporter_id' => 'required|integer|exists:users,id',
            'assignee_id' => 'required|integer|exists:users,id',
            'due_date' => [
                'required',
                Rule::date()->format('Y-m-d')->todayOrAfter()
            ],
            'order' => 'sometimes|required|integer'
        ]);
        $taskData = $request->only([
            'title',
            'description',
            'priority',
            'reporter_id',
            'assignee_id',
            'due_date',
            'order'
        ]);

        $status = Status::where('name', self::INITIAL_STATUS)->first();

        if (!$status) {
            return response()->json([
                'message' =>
                "The '" . self::INITIAL_STATUS . "' status is not exist."
            ], 500);
        }
        $taskData['status_id'] = $status->value('id');

        return Task::create($taskData);
    }

    public function index()
    {
        return response()->json(Task::all());
    }
}
