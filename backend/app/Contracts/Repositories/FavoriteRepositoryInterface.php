<?php

namespace App\Contracts\Repositories;

use App\Models\Favorite;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface FavoriteRepositoryInterface
{
    public function getUserFavorites(User $user): Collection;

    public function create(User $user, array $data): Favorite;

    public function deleteByMovieId(User $user, int $movieId): bool;

    public function isFavorite(User $user, int $movieId): bool;

    public function findByMovieId(User $user, int $movieId): ?Favorite;
}
