<?php

namespace App\Contracts\Services;

use App\Models\Favorite;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface FavoriteServiceInterface
{
    public function getUserFavorites(User $user): Collection;

    public function addFavorite(User $user, array $movieData): Favorite;

    public function removeFavorite(User $user, int $movieId): bool;

    public function isFavorite(User $user, int $movieId): bool;
}
