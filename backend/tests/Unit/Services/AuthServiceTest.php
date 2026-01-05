<?php

namespace Tests\Unit\Services;

use App\Contracts\Repositories\UserRepositoryInterface;
use App\Exceptions\AuthException;
use App\Models\User;
use App\Services\AuthService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthServiceTest extends TestCase
{
    use RefreshDatabase;

    private AuthService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(AuthService::class);
    }

    public function test_can_register_new_user(): void
    {
        $result = $this->service->register('John Doe', 'john@example.com', 'password123');

        $this->assertArrayHasKey('user', $result);
        $this->assertArrayHasKey('token', $result);
        $this->assertArrayHasKey('token_type', $result);
        $this->assertEquals('Bearer', $result['token_type']);
        $this->assertEquals('John Doe', $result['user']->name);
        $this->assertEquals('john@example.com', $result['user']->email);
        $this->assertDatabaseHas('users', [
            'email' => 'john@example.com',
        ]);
    }

    public function test_cannot_register_with_existing_email(): void
    {
        User::factory()->create(['email' => 'existing@example.com']);

        $this->expectException(AuthException::class);
        $this->expectExceptionMessage('Email already registered');

        $this->service->register('Test User', 'existing@example.com', 'password123');
    }

    public function test_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        $result = $this->service->login('test@example.com', 'password123');

        $this->assertArrayHasKey('user', $result);
        $this->assertArrayHasKey('token', $result);
        $this->assertArrayHasKey('token_type', $result);
        $this->assertEquals('Bearer', $result['token_type']);
        $this->assertEquals($user->id, $result['user']->id);
    }

    public function test_cannot_login_with_invalid_email(): void
    {
        $this->expectException(AuthException::class);
        $this->expectExceptionMessage('Invalid credentials');

        $this->service->login('nonexistent@example.com', 'password123');
    }

    public function test_cannot_login_with_invalid_password(): void
    {
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('correctpassword'),
        ]);

        $this->expectException(AuthException::class);
        $this->expectExceptionMessage('Invalid credentials');

        $this->service->login('test@example.com', 'wrongpassword');
    }

    public function test_can_get_authenticated_user(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $this->actingAs($user);

        $authenticatedUser = $this->service->getAuthenticatedUser();

        $this->assertNotNull($authenticatedUser);
        $this->assertEquals($user->id, $authenticatedUser->id);
    }

    public function test_can_logout_user(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $this->actingAs($user);
        $user->createToken('test-token');

        $result = $this->service->logout();

        $this->assertTrue($result);
    }

    public function test_password_is_hashed_on_registration(): void
    {
        $result = $this->service->register('Test User', 'test@example.com', 'plainpassword');

        $this->assertTrue(Hash::check('plainpassword', $result['user']->password));
        $this->assertNotEquals('plainpassword', $result['user']->password);
    }
}
