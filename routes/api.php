<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/all-users', [AuthController::class, 'getAllUsers']);
    Route::get('/getUserTask/{userId}', [TaskController::class, 'index']);
    Route::get('/{taskId}', [TaskController::class, 'show']);
    Route::post('/createTask', [TaskController::class, 'store']);
    Route::put('/{taskId}', [TaskController::class, 'update']);
    Route::delete('/{taskId}', [TaskController::class, 'destroy']);
