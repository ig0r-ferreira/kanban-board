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

    function index() {
        return response()->json(Status::all());
    }
}
