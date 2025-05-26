<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Status;

class BoardController extends Controller
{
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
