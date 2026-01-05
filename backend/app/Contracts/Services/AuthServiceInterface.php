<?php

namespace App\Contracts\Services;

interface AuthServiceInterface
{
    public function register(string $name, string $email, string $password): array;

    public function login(string $email, string $password): array;

    public function getAuthenticatedUser();

    public function logout(): bool;
}
