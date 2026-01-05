<?php

namespace App\Contracts\Repositories;

use App\Models\Favorite;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface FavoriteRepositoryInterface
{
    /**
     * Get all favorites for a user
     */
    public function getUserFavorites(User $user): Collection;

    /**
     * Create a favorite for a user
     */
    public function create(User $user, array $data): Favorite;

    /**
     * Delete a favorite by movie ID
     */
    public function deleteByMovieId(User $user, int $movieId): bool;

    /**
     * Check if a movie is favorited by user
     */
    public function isFavorite(User $user, int $movieId): bool;

    /**
     * Find favorite by movie ID
     */
    public function findByMovieId(User $user, int $movieId): ?Favorite;
}
