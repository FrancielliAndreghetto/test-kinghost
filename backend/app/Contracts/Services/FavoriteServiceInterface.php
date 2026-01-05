<?php

namespace App\Contracts\Services;

use App\Models\Favorite;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface FavoriteServiceInterface
{
    /**
     * Get all favorites for a user
     */
    public function getUserFavorites(User $user): Collection;

    /**
     * Add a movie to favorites
     */
    public function addFavorite(User $user, array $movieData): Favorite;

    /**
     * Remove a movie from favorites
     */
    public function removeFavorite(User $user, int $movieId): bool;

    /**
     * Check if a movie is in user's favorites
     */
    public function isFavorite(User $user, int $movieId): bool;
}
