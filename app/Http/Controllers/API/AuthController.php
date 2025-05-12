<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    function register (Request $request){
        return new UserController()->store($request);
    }

    function login(Request $request){
        $request->validate(UserController::getLoginRules());

        $credentials = $request->only(['email', 'password']);

        if (!Auth::attempt($credentials)){
            return response()->json(['message' => 'Invalid credentials.'], 401);
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
