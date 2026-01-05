<?php

namespace Database\Factories;

use App\Models\Favorite;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class FavoriteFactory extends Factory
{
    protected $model = Favorite::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'movie_id' => $this->faker->numberBetween(1, 10000),
            'movie_title' => $this->faker->sentence(3),
            'poster_path' => '/poster_' . $this->faker->uuid() . '.jpg',
            'overview' => $this->faker->paragraph(),
            'vote_average' => $this->faker->randomFloat(1, 0, 10),
            'release_date' => $this->faker->date(),
            'genre_ids' => json_encode([
                $this->faker->numberBetween(1, 20),
                $this->faker->numberBetween(1, 20),
            ]),
        ];
    }
}
