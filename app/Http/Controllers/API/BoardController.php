<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\ColumnResource;
use App\Models\Status;

class BoardController extends Controller
{
    public function index()
    {

        $statuses = Status::with([
            'tasks' => function ($query) {
                $query
                    ->orderBy('order')
                    ->with(['status', 'assignee', 'reporter']);
            }
        ])->orderBy('order')->get();

        return ColumnResource::collection($statuses);
    }
}
