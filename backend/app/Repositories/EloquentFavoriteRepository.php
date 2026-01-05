<?php

namespace App\Repositories;

use App\Contracts\Repositories\FavoriteRepositoryInterface;
use App\Models\Favorite;
use Illuminate\Database\Eloquent\Collection;

class EloquentFavoriteRepository implements FavoriteRepositoryInterface
{
    public function getUserFavorites(int $userId): Collection
    {
        return Favorite::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function create(int $userId, array $data): Favorite
    {
        return Favorite::create([
            'user_id' => $userId,
            ...$data,
        ]);
    }

    public function deleteByMovieId(int $userId, int $movieId): bool
    {
        return Favorite::where('user_id', $userId)
            ->where('movie_id', $movieId)
            ->delete() > 0;
    }

    public function isFavorite(int $userId, int $movieId): bool
    {
        return Favorite::where('user_id', $userId)
            ->where('movie_id', $movieId)
            ->exists();
    }

    public function findByMovieId(int $userId, int $movieId): ?Favorite
    {
        return Favorite::where('user_id', $userId)
            ->where('movie_id', $movieId)
            ->first();
    }
}
