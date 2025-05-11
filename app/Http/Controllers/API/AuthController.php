<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

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

    function login(Request $request){
        $credentials = $request->only(['email', 'password']);

        if (!Auth::attempt($credentials)){
            abort(401, 'Invalid credentials.');
        }

        $token = $request->user()->createToken('auth_token')->plainTextToken;

        return response()->json([
            'auth_token' => $token,
            'token_type' => 'Bearer'
        ]);
    }

    function logout(Request $request){
        $request->user('sanctum')->currentAccessToken()->delete();
        
        return response()->json([], 204);
    }
}
