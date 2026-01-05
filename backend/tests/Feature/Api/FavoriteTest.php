<?php

namespace Tests\Feature\Api;

use App\Models\Favorite;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FavoriteTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private string $token;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->token = $this->user->createToken('test-token')->plainTextToken;
    }

    private function headers(): array
    {
        return ['Authorization' => "Bearer {$this->token}"];
    }

    public function test_can_get_user_favorites(): void
    {
        Favorite::factory()->count(3)->create(['user_id' => $this->user->id]);

        $response = $this->withHeaders($this->headers())
            ->getJson('/api/favorites');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'favorites' => [
                    '*' => [
                        'id',
                        'movie_id',
                        'movie_title',
                        'poster_path',
                        'overview',
                        'vote_average',
                        'release_date',
                        'genre_ids',
                    ],
                ],
            ])
            ->assertJson(['success' => true]);

        $this->assertCount(3, $response->json('favorites'));
    }

    public function test_get_favorites_requires_authentication(): void
    {
        $response = $this->getJson('/api/favorites');

        $response->assertStatus(401);
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

        $response = $this->withHeaders($this->headers())
            ->postJson('/api/favorites', $movieData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'favorite' => [
                    'id',
                    'movie_id',
                    'movie_title',
                ],
                'message',
            ])
            ->assertJson([
                'success' => true,
                'favorite' => [
                    'movie_id' => 123,
                    'movie_title' => 'Test Movie',
                ],
            ]);

        $this->assertDatabaseHas('favorites', [
            'user_id' => $this->user->id,
            'movie_id' => 123,
        ]);
    }

    public function test_add_favorite_requires_authentication(): void
    {
        $response = $this->postJson('/api/favorites', [
            'movie_id' => 123,
            'movie_title' => 'Test Movie',
        ]);

        $response->assertStatus(401);
    }

    public function test_add_favorite_validates_required_fields(): void
    {
        $response = $this->withHeaders($this->headers())
            ->postJson('/api/favorites', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['movie_id', 'movie_title']);
    }

    public function test_cannot_add_duplicate_favorite(): void
    {
        $movieData = [
            'movie_id' => 123,
            'movie_title' => 'Test Movie',
            'poster_path' => '/poster.jpg',
        ];

        Favorite::factory()->create([
            'user_id' => $this->user->id,
            'movie_id' => 123,
        ]);

        $response = $this->withHeaders($this->headers())
            ->postJson('/api/favorites', $movieData);

        $response->assertStatus(409)
            ->assertJson([
                'success' => false,
            ]);
    }

    public function test_can_remove_favorite(): void
    {
        Favorite::factory()->create([
            'user_id' => $this->user->id,
            'movie_id' => 123,
        ]);

        $response = $this->withHeaders($this->headers())
            ->deleteJson('/api/favorites/123');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseMissing('favorites', [
            'user_id' => $this->user->id,
            'movie_id' => 123,
        ]);
    }

    public function test_remove_favorite_requires_authentication(): void
    {
        $response = $this->deleteJson('/api/favorites/123');

        $response->assertStatus(401);
    }

    public function test_cannot_remove_nonexistent_favorite(): void
    {
        $response = $this->withHeaders($this->headers())
            ->deleteJson('/api/favorites/999');

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
            ]);
    }

    public function test_can_check_if_movie_is_favorite(): void
    {
        Favorite::factory()->create([
            'user_id' => $this->user->id,
            'movie_id' => 123,
        ]);

        $response = $this->withHeaders($this->headers())
            ->getJson('/api/favorites/123/check');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'is_favorite' => true,
            ]);
    }

    public function test_check_favorite_returns_false_for_nonexistent(): void
    {
        $response = $this->withHeaders($this->headers())
            ->getJson('/api/favorites/999/check');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'is_favorite' => false,
            ]);
    }

    public function test_check_favorite_requires_authentication(): void
    {
        $response = $this->getJson('/api/favorites/123/check');

        $response->assertStatus(401);
    }

    public function test_user_can_only_see_own_favorites(): void
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

        $response = $this->withHeaders($this->headers())
            ->getJson('/api/favorites');

        $favorites = $response->json('favorites');
        $this->assertCount(1, $favorites);
        $this->assertEquals(123, $favorites[0]['movie_id']);
    }
}
