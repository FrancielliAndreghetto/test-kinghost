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

    /**
     * Get all favorites for a user
     */
    public function getUserFavorites(User $user): Collection
    {
        return $this->favoriteRepository->getUserFavorites($user);
    }

    /**
     * Add a movie to favorites
     */
    public function addFavorite(User $user, array $movieData): Favorite
    {
        // Business rule: Check if already exists
        if ($this->favoriteRepository->isFavorite($user, $movieData['movie_id'])) {
            throw new \DomainException('Movie already in favorites');
        }

        // Business rule: Validate required fields
        $this->validateMovieData($movieData);

        return $this->favoriteRepository->create($user, $movieData);
    }

    /**
     * Remove a movie from favorites
     */
    public function removeFavorite(User $user, int $movieId): bool
    {
        // Business rule: Check if exists before deleting
        if (!$this->favoriteRepository->isFavorite($user, $movieId)) {
            throw new \DomainException('Movie not found in favorites');
        }

        return $this->favoriteRepository->deleteByMovieId($user, $movieId);
    }

    /**
     * Check if a movie is in user's favorites
     */
    public function isFavorite(User $user, int $movieId): bool
    {
        return $this->favoriteRepository->isFavorite($user, $movieId);
    }

    /**
     * Validate movie data
     */
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
