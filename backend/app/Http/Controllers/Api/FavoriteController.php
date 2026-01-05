<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Services\FavoriteServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFavoriteRequest;
use App\Http\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        private readonly FavoriteServiceInterface $favoriteService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $favorites = $this->favoriteService->getUserFavorites($request->user()->id);

        return $this->successResponse([
            'favorites' => $favorites,
        ]);
    }

    public function store(StoreFavoriteRequest $request): JsonResponse
    {
        try {
            $favorite = $this->favoriteService->addFavorite(
                $request->user()->id,
                $request->validated()
            );

            return $this->successResponse([
                'favorite' => $favorite,
                'message' => 'Movie added to favorites',
            ], 201);
        } catch (\DomainException $e) {
            return $this->errorResponse($e->getMessage(), 409);
        }
    }

    public function destroy(Request $request, int $movieId): JsonResponse
    {
        try {
            $this->favoriteService->removeFavorite($request->user()->id, $movieId);

            return $this->successResponse([
                'message' => 'Movie removed from favorites',
            ]);
        } catch (\DomainException $e) {
            return $this->errorResponse($e->getMessage(), 404);
        }
    }

    public function check(Request $request, int $movieId): JsonResponse
    {
        $isFavorite = $this->favoriteService->isFavorite($request->user()->id, $movieId);

        return $this->successResponse([
            'is_favorite' => $isFavorite,
        ]);
    }
}