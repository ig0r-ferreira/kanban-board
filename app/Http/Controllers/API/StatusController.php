<?php

namespace App\Http\Controllers\API;

use App\Models\Status;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StatusController extends Controller
{
    public function store(Request $request){

        $request->validate([
            'name' => 'required|string|max:30|unique:statuses',
            'order' => 'sometimes|required|integer'
        ]);

        return Status::create($request->only(['name', 'order']));
    }

    public function index()
    {
        $statuses = Status::with([
            'tasks' => function ($query) {
                $query->orderBy('order');
            },
            'tasks.status',
            'tasks.assignee',
            'tasks.reporter',
        ])->orderBy('order')->get();

        return response()->json($statuses);
    }
}
