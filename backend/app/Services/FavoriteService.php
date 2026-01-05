<?php

namespace App\Services;

use App\Contracts\Repositories\FavoriteRepositoryInterface;
use App\Contracts\Services\FavoriteServiceInterface;
use App\Models\Favorite;
use Illuminate\Database\Eloquent\Collection;

class FavoriteService implements FavoriteServiceInterface
{
    public function __construct(
        private readonly FavoriteRepositoryInterface $favoriteRepository
    ) {}

    public function getUserFavorites(int $userId): Collection
    {
        return $this->favoriteRepository->getUserFavorites($userId);
    }

    public function addFavorite(int $userId, array $movieData): Favorite
    {
        if ($this->favoriteRepository->isFavorite($userId, $movieData['movie_id'])) {
            throw new \DomainException('Movie already in favorites');
        }

        return $this->favoriteRepository->create($userId, $movieData);
    }

    public function removeFavorite(int $userId, int $movieId): bool
    {
        if (!$this->favoriteRepository->isFavorite($userId, $movieId)) {
            throw new \DomainException('Movie not found in favorites');
        }

        return $this->favoriteRepository->deleteByMovieId($userId, $movieId);
    }

    public function isFavorite(int $userId, int $movieId): bool
    {
        return $this->favoriteRepository->isFavorite($userId, $movieId);
    }
}
