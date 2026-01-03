<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MovieController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Debug route to check users in database
Route::get('/debug/users', function () {
    return response()->json([
        'total_users' => User::count(),
        'users' => User::select('id', 'name', 'email', 'created_at')->get()
    ]);
});

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
    Route::get('/me', [AuthController::class, 'me'])->middleware('auth:sanctum');
});

Route::prefix('movies')->group(function () {
    Route::get('/search', [MovieController::class, 'search']);
    Route::get('/genres', [MovieController::class, 'genres']);
    Route::get('/popular', [MovieController::class, 'popular']);
    Route::get('/top-rated', [MovieController::class, 'topRated']);
    Route::get('/now-playing', [MovieController::class, 'nowPlaying']);
    Route::get('/upcoming', [MovieController::class, 'upcoming']);
    Route::get('/{id}', [MovieController::class, 'show']);
});

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});
