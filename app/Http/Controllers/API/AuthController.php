<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Controllers\Controller;


class AuthController extends Controller
{
    function register (Request $request){
        $request->validate([
            'name' => 'required|string|max:50',
            'email' => 'required|string|email:rfc,dns|unique:users',
            'password' => 'required|string|min:8'
        ]);
    
        $newUser = User::create($request->only(['name', 'email', 'password']));
        
        return $newUser;   
    }
}
