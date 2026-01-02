<?php

return [
    /*
    |--------------------------------------------------------------------------
    | TMDB API Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for The Movie Database API integration
    |
    */

    'api_key' => env('TMDB_API_KEY', ''),
    'api_token' => env('TMDB_API_TOKEN', ''),
    'base_url' => env('TMDB_BASE_URL', 'https://api.themoviedb.org/3'),
    'image_base_url' => env('TMDB_IMAGE_BASE_URL', 'https://image.tmdb.org/t/p'),
    'timeout' => env('TMDB_TIMEOUT', 30),
];
