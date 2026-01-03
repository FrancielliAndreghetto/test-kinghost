<?php

namespace App\Contracts\Services;

interface AuthServiceInterface
{
    /**
     * Register a new user
     */
    public function register(string $name, string $email, string $password): array;

    /**
     * Login user and return token
     */
    public function login(string $email, string $password): array;

    /**
     * Get authenticated user
     */
    public function getAuthenticatedUser();

    /**
     * Logout user
     */
    public function logout(): bool;
}
