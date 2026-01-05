<?php

namespace App\Repositories;

use App\Contracts\Repositories\FavoriteRepositoryInterface;
use App\Models\Favorite;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class EloquentFavoriteRepository implements FavoriteRepositoryInterface
{
    public function getUserFavorites(User $user): Collection
    {
        return $user->favorites()
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function create(User $user, array $data): Favorite
    {
        return $user->favorites()->create($data);
    }

    public function deleteByMovieId(User $user, int $movieId): bool
    {
        return $user->favorites()
            ->where('movie_id', $movieId)
            ->delete() > 0;
    }

    public function isFavorite(User $user, int $movieId): bool
    {
        return $user->favorites()
            ->where('movie_id', $movieId)
            ->exists();
    }

    public function findByMovieId(User $user, int $movieId): ?Favorite
    {
        return $user->favorites()
            ->where('movie_id', $movieId)
            ->first();
    }
}
