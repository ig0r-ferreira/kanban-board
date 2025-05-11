<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:50',
            'email' => 'required|string|email:rfc,dns|unique:users',
            'password' => 'required|string|min:8'
        ]);

        return User::create($request->only(['name', 'email', 'password']));
    }
}
