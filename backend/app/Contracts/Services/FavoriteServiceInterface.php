<?php

namespace App\Contracts\Services;

use App\Models\Favorite;
use Illuminate\Database\Eloquent\Collection;

interface FavoriteServiceInterface
{
    public function getUserFavorites(int $userId): Collection;

    public function addFavorite(int $userId, array $movieData): Favorite;

    public function removeFavorite(int $userId, int $movieId): bool;

    public function isFavorite(int $userId, int $movieId): bool;
}
