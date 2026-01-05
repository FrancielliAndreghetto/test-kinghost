<?php

namespace Tests\Unit\Services;

use App\Contracts\Repositories\FavoriteRepositoryInterface;
use App\Models\Favorite;
use App\Models\User;
use App\Services\FavoriteService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FavoriteServiceTest extends TestCase
{
    use RefreshDatabase;

    private FavoriteService $service;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(FavoriteService::class);
        $this->user = User::factory()->create();
    }

    public function test_can_get_user_favorites(): void
    {
        Favorite::factory()->count(3)->create(['user_id' => $this->user->id]);

        $favorites = $this->service->getUserFavorites($this->user->id);

        $this->assertCount(3, $favorites);
    }

    public function test_can_add_favorite(): void
    {
        $movieData = [
            'movie_id' => 123,
            'movie_title' => 'Test Movie',
            'poster_path' => '/poster.jpg',
            'overview' => 'Test overview',
            'vote_average' => 8.5,
            'release_date' => '2024-01-01',
            'genre_ids' => [1, 2, 3],
        ];

        $favorite = $this->service->addFavorite($this->user->id, $movieData);

        $this->assertNotNull($favorite);
        $this->assertEquals(123, $favorite->movie_id);
        $this->assertDatabaseHas('favorites', [
            'user_id' => $this->user->id,
            'movie_id' => 123,
        ]);
    }

    public function test_cannot_add_duplicate_favorite(): void
    {
        $movieData = [
            'movie_id' => 123,
            'movie_title' => 'Test Movie',
            'poster_path' => '/poster.jpg',
            'overview' => 'Test overview',
            'vote_average' => 8.5,
            'release_date' => '2024-01-01',
            'genre_ids' => [1, 2, 3],
        ];

        Favorite::factory()->create([
            'user_id' => $this->user->id,
            'movie_id' => 123,
        ]);

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Movie already in favorites');

        $this->service->addFavorite($this->user->id, $movieData);
    }

    public function test_can_remove_favorite(): void
    {
        Favorite::factory()->create([
            'user_id' => $this->user->id,
            'movie_id' => 123,
        ]);

        $result = $this->service->removeFavorite($this->user->id, 123);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('favorites', [
            'user_id' => $this->user->id,
            'movie_id' => 123,
        ]);
    }

    public function test_cannot_remove_nonexistent_favorite(): void
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Movie not found in favorites');

        $this->service->removeFavorite($this->user->id, 999);
    }

    public function test_can_check_if_movie_is_favorite(): void
    {
        Favorite::factory()->create([
            'user_id' => $this->user->id,
            'movie_id' => 123,
        ]);

        $isFavorite = $this->service->isFavorite($this->user->id, 123);
        $isNotFavorite = $this->service->isFavorite($this->user->id, 999);

        $this->assertTrue($isFavorite);
        $this->assertFalse($isNotFavorite);
    }
}
