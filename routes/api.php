<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;


Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
});


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);


Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);



Route::get('/get-users', [UserController::class, 'list']);

Route::get('/get-user-by-id/{id}', [UserController::class, 'getUserById']);



Route::put('/update-user/{id}', [UserController::class, 'updateUser']);

Route::delete('/delete-user/{id}', [UserController::class, 'deleteUser']);





