<?php

namespace Tests\Unit\Repositories;

use App\Models\User;
use App\Repositories\EloquentUserRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EloquentUserRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private EloquentUserRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new EloquentUserRepository();
    }

    public function test_can_find_user_by_email(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
        ]);

        $found = $this->repository->findByEmail('test@example.com');

        $this->assertNotNull($found);
        $this->assertEquals($user->id, $found->id);
        $this->assertEquals('test@example.com', $found->email);
    }

    public function test_returns_null_when_user_not_found_by_email(): void
    {
        $found = $this->repository->findByEmail('nonexistent@example.com');

        $this->assertNull($found);
    }

    public function test_can_find_user_by_id(): void
    {
        $user = User::factory()->create();

        $found = $this->repository->findById($user->id);

        $this->assertNotNull($found);
        $this->assertEquals($user->id, $found->id);
    }

    public function test_returns_null_when_user_not_found_by_id(): void
    {
        $found = $this->repository->findById(99999);

        $this->assertNull($found);
    }

    public function test_can_create_user(): void
    {
        $data = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password'),
        ];

        $user = $this->repository->create($data);

        $this->assertNotNull($user);
        $this->assertEquals('John Doe', $user->name);
        $this->assertEquals('john@example.com', $user->email);
        $this->assertDatabaseHas('users', [
            'email' => 'john@example.com',
        ]);
    }

    public function test_can_update_user(): void
    {
        $user = User::factory()->create([
            'name' => 'Old Name',
        ]);

        $updated = $this->repository->update($user, [
            'name' => 'New Name',
        ]);

        $this->assertEquals('New Name', $updated->name);
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'New Name',
        ]);
    }
}
