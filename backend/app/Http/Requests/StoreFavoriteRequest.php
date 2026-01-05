<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFavoriteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'movie_id' => 'required|integer',
            'movie_title' => 'required|string',
            'poster_path' => 'nullable|string',
            'backdrop_path' => 'nullable|string',
            'overview' => 'nullable|string',
            'vote_average' => 'nullable|numeric',
            'release_date' => 'nullable|date',
            'genre_ids' => 'nullable|array',
        ];
    }
}
