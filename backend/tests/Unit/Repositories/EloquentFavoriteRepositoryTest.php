<?php

namespace Tests\Unit\Repositories;

use App\Models\Favorite;
use App\Models\User;
use App\Repositories\EloquentFavoriteRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EloquentFavoriteRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private EloquentFavoriteRepository $repository;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new EloquentFavoriteRepository();
        $this->user = User::factory()->create();
    }

    public function test_can_get_user_favorites(): void
    {
        Favorite::factory()->count(3)->create(['user_id' => $this->user->id]);

        $favorites = $this->repository->getUserFavorites($this->user);

        $this->assertCount(3, $favorites);
    }

    public function test_favorites_are_ordered_by_created_at_desc(): void
    {
        $favorite1 = Favorite::factory()->create([
            'user_id' => $this->user->id,
            'created_at' => now()->subDays(2),
        ]);
        $favorite2 = Favorite::factory()->create([
            'user_id' => $this->user->id,
            'created_at' => now()->subDays(1),
        ]);
        $favorite3 = Favorite::factory()->create([
            'user_id' => $this->user->id,
            'created_at' => now(),
        ]);

        $favorites = $this->repository->getUserFavorites($this->user);

        $this->assertEquals($favorite3->id, $favorites->first()->id);
        $this->assertEquals($favorite1->id, $favorites->last()->id);
    }

    public function test_can_create_favorite(): void
    {
        $data = [
            'movie_id' => 123,
            'movie_title' => 'Test Movie',
            'poster_path' => '/poster.jpg',
            'overview' => 'Test overview',
            'vote_average' => 8.5,
            'release_date' => '2024-01-01',
            'genre_ids' => json_encode([1, 2, 3]),
        ];

        $favorite = $this->repository->create($this->user, $data);

        $this->assertNotNull($favorite);
        $this->assertEquals(123, $favorite->movie_id);
        $this->assertEquals('Test Movie', $favorite->movie_title);
        $this->assertDatabaseHas('favorites', [
            'user_id' => $this->user->id,
            'movie_id' => 123,
        ]);
    }

    public function test_can_delete_favorite_by_movie_id(): void
    {
        Favorite::factory()->create([
            'user_id' => $this->user->id,
            'movie_id' => 123,
        ]);

        $result = $this->repository->deleteByMovieId($this->user, 123);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('favorites', [
            'user_id' => $this->user->id,
            'movie_id' => 123,
        ]);
    }

    public function test_returns_false_when_deleting_nonexistent_favorite(): void
    {
        $result = $this->repository->deleteByMovieId($this->user, 999);

        $this->assertFalse($result);
    }

    public function test_can_check_if_movie_is_favorite(): void
    {
        Favorite::factory()->create([
            'user_id' => $this->user->id,
            'movie_id' => 123,
        ]);

        $isFavorite = $this->repository->isFavorite($this->user, 123);
        $isNotFavorite = $this->repository->isFavorite($this->user, 999);

        $this->assertTrue($isFavorite);
        $this->assertFalse($isNotFavorite);
    }

    public function test_can_find_favorite_by_movie_id(): void
    {
        $favorite = Favorite::factory()->create([
            'user_id' => $this->user->id,
            'movie_id' => 123,
        ]);

        $found = $this->repository->findByMovieId($this->user, 123);

        $this->assertNotNull($found);
        $this->assertEquals($favorite->id, $found->id);
    }

    public function test_returns_null_when_favorite_not_found(): void
    {
        $found = $this->repository->findByMovieId($this->user, 999);

        $this->assertNull($found);
    }

    public function test_only_returns_favorites_for_specific_user(): void
    {
        $otherUser = User::factory()->create();
        
        Favorite::factory()->create([
            'user_id' => $this->user->id,
            'movie_id' => 123,
        ]);
        Favorite::factory()->create([
            'user_id' => $otherUser->id,
            'movie_id' => 456,
        ]);

        $userFavorites = $this->repository->getUserFavorites($this->user);

        $this->assertCount(1, $userFavorites);
        $this->assertEquals(123, $userFavorites->first()->movie_id);
    }
}
