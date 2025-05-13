<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    const REGISTRATION_RULES = [
        'name' => 'required|string|max:50',
        'email' => 'required|string|email:rfc,dns|unique:users',
        'password' => 'required|string|min:8'
    ];

    const LOGIN_RULES = [
        'email' => 'required|string|email:rfc,dns',
        'password' => 'required|string|min:8'
    ];

    public function store(Request $request)
    {
        $request->validate(self::REGISTRATION_RULES);
        return User::create($request->only(['name', 'email', 'password']));
    }
}
