<?php

use App\Http\Controllers\Api\MovieController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Movies routes
Route::prefix('movies')->group(function () {
    Route::get('/search', [MovieController::class, 'search']);
    Route::get('/popular', [MovieController::class, 'popular']);
    Route::get('/now-playing', [MovieController::class, 'nowPlaying']);
    Route::get('/upcoming', [MovieController::class, 'upcoming']);
    Route::get('/top-rated', [MovieController::class, 'topRated']);
    Route::get('/genres', [MovieController::class, 'genres']);
    Route::get('/{id}', [MovieController::class, 'show']);
});
