<?php

namespace App\Repositories;

use App\Contracts\Repositories\FavoriteRepositoryInterface;
use App\Models\Favorite;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class EloquentFavoriteRepository implements FavoriteRepositoryInterface
{
    /**
     * Get all favorites for a user
     */
    public function getUserFavorites(User $user): Collection
    {
        return $user->favorites()
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Create a favorite for a user
     */
    public function create(User $user, array $data): Favorite
    {
        return $user->favorites()->create($data);
    }

    /**
     * Delete a favorite by movie ID
     */
    public function deleteByMovieId(User $user, int $movieId): bool
    {
        return $user->favorites()
            ->where('movie_id', $movieId)
            ->delete() > 0;
    }

    /**
     * Check if a movie is favorited by user
     */
    public function isFavorite(User $user, int $movieId): bool
    {
        return $user->favorites()
            ->where('movie_id', $movieId)
            ->exists();
    }

    /**
     * Find favorite by movie ID
     */
    public function findByMovieId(User $user, int $movieId): ?Favorite
    {
        return $user->favorites()
            ->where('movie_id', $movieId)
            ->first();
    }
}
