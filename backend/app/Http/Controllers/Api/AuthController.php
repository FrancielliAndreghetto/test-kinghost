<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Services\AuthServiceInterface;
use App\Exceptions\AuthException;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        private readonly AuthServiceInterface $authService
    ) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            $result = $this->authService->register(
                $request->validated('name'),
                $request->validated('email'),
                $request->validated('password')
            );

            return $this->successResponse([
                'user' => $result['user'],
                'token' => $result['token'],
                'token_type' => $result['token_type'],
            ], 201);
        } catch (AuthException $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode());
        } catch (\Exception $e) {
            return $this->errorResponse('Registration failed', 500);
        }
    }

    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $result = $this->authService->login(
                $request->validated('email'),
                $request->validated('password')
            );

            return $this->successResponse([
                'user' => $result['user'],
                'token' => $result['token'],
                'token_type' => $result['token_type'],
            ]);
        } catch (AuthException $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode());
        } catch (\Exception $e) {
            return $this->errorResponse('Login failed', 500);
        }
    }

    public function me(Request $request): JsonResponse
    {
        return $this->successResponse([
            'user' => $request->user(),
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        try {
            $this->authService->logout();

            return $this->successResponse([
                'message' => 'Logged out successfully',
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Logout failed', 500);
        }
    }
}
