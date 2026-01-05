<?php

namespace App\Services;

use App\Contracts\Repositories\FavoriteRepositoryInterface;
use App\Contracts\Services\FavoriteServiceInterface;
use App\Models\Favorite;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class FavoriteService implements FavoriteServiceInterface
{
    public function __construct(
        private readonly FavoriteRepositoryInterface $favoriteRepository
    ) {}

    public function getUserFavorites(User $user): Collection
    {
        return $this->favoriteRepository->getUserFavorites($user);
    }

    public function addFavorite(User $user, array $movieData): Favorite
    {
        $this->validateMovieData($movieData);

        if ($this->favoriteRepository->isFavorite($user, $movieData['movie_id'])) {
            throw new \DomainException('Movie already in favorites');
        }

        return $this->favoriteRepository->create($user, $movieData);
    }

    public function removeFavorite(User $user, int $movieId): bool
    {
        if (!$this->favoriteRepository->isFavorite($user, $movieId)) {
            throw new \DomainException('Movie not found in favorites');
        }

        return $this->favoriteRepository->deleteByMovieId($user, $movieId);
    }

    public function isFavorite(User $user, int $movieId): bool
    {
        return $this->favoriteRepository->isFavorite($user, $movieId);
    }

    private function validateMovieData(array $data): void
    {
        $required = ['movie_id', 'movie_title'];
        
        foreach ($required as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                throw new \InvalidArgumentException("Field {$field} is required");
            }
        }
    }
}
