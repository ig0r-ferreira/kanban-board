<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * @var array<string, string>
     */
    protected static $registrationRules = [
        'name' => 'required|string|max:50',
        'email' => 'required|string|email:rfc,dns|unique:users',
        'password' => 'required|string|min:8'
    ];

    protected static $loginRules = [
        'email' => 'required|string|email:rfc,dns',
        'password' => 'required|string|min:8'
    ];

    public function store(Request $request)
    {
        $request->validate(static::getRegistrationRules());
        return User::create($request->only(['name', 'email', 'password']));
    }

    public static function getRegistrationRules()
    {
        return static::$registrationRules;
    }

    public static function getLoginRules()
    {
        return static::$loginRules;
    }

}
