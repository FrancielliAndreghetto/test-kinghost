<?php

namespace App\Contracts\Repositories;

use App\Models\Favorite;
use Illuminate\Database\Eloquent\Collection;

interface FavoriteRepositoryInterface
{
    public function getUserFavorites(int $userId): Collection;

    public function create(int $userId, array $data): Favorite;

    public function deleteByMovieId(int $userId, int $movieId): bool;

    public function isFavorite(int $userId, int $movieId): bool;

    public function findByMovieId(int $userId, int $movieId): ?Favorite;
}
