<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Event;
use App\Models\User;
use App\Models\Statistic;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Statistic>
 */
class StatisticFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'event_id' => Event::factory(),
            'visitor_views' => [
                [
                    'date'      => $this->faker->dateTimeBetween('now', '+1 hour'),
                    'sessionId' => $this->faker->regexify('[A-Za-z0-9]{20}'),
                ],
            ],
            'user_likes' => [
                [
                    'userId' => User::factory(),
                    'date'   => $this->faker->dateTimeBetween('now', '+1 hour'),
                ],
            ],
            'views' => rand(0, 100),
            'likes' => fn (array $attributes) => $this->faker->numberBetween(0, $attributes['views']),
        ];
    }
}
