<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\StatusController;
use App\Http\Controllers\API\TaskController;
use App\Http\Controllers\API\UserController;
use Illuminate\Support\Facades\Route;


Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout'])
        ->middleware('auth:sanctum');
    Route::get('user', [AuthController::class, 'user'])
        ->middleware('auth:sanctum');
});

Route::post('statuses', [StatusController::class, 'store'])
    ->middleware('auth:sanctum');
Route::get('statuses', [StatusController::class, 'index'])
    ->middleware('auth:sanctum');

Route::post('tasks', [TaskController::class, 'store'])
    ->middleware('auth:sanctum');
Route::get('tasks', [TaskController::class, 'index'])
    ->middleware('auth:sanctum');
Route::patch('tasks/{id}', [TaskController::class, 'update'])
    ->middleware('auth:sanctum');

Route::get('users', [UserController::class, 'index'])
    ->middleware('auth:sanctum');
