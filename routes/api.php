<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthApiController;

// demo register for testing
Route::post('/register', [AuthApiController::class, 'register']);

// login via password grant (server-side proxy)
Route::post('/login', [AuthApiController::class, 'login']);
Route::post('/refresh', [AuthApiController::class, 'refresh']);

Route::middleware('auth:api')->group(function () {
    Route::get('/me', fn (Request $r) => $r->user());
    Route::post('/logout', [AuthApiController::class, 'logout']);
});