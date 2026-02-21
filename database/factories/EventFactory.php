<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

use App\Models\Category;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $start = fake()->dateTimeBetween('now', '+1 month');
        $end = fake()->boolean(80) 
            ? fake()->dateTimeBetween($start, (clone $start)->modify('+1 week')) 
            : null;

        return [
            'title' => fake()->sentence(),
            'user_id' => User::Factory(),
            'category_id' => Category::factory(),
            'start_time' => $start,
            'end_time' => $end,
            'content' => fake()->paragraph(),
            'status' => fake()->boolean(80),
        ];
    }
}
