<?php

namespace App\Http\Controllers\API;

use App\Models\Status;
use App\Http\Controllers\Controller;
use App\Http\Resources\StatusResource;
use Illuminate\Http\Request;

class StatusController extends Controller
{
    public function store(Request $request){

        $request->validate([
            'name' => 'required|string|max:30|unique:statuses',
            'order' => 'sometimes|required|integer'
        ]);
        $newStatus = Status::create($request->only(['name', 'order']));
        return new StatusResource($newStatus);
    }

    function index() {
        return StatusResource::collection(Status::all());
    }
}
