<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Testimonial>
 */
class TestimonialFactory extends Factory
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
            'user_id' => User::factory(),
            'content' => $this->faker->paragraph(),
            'rating' => fake()->numberBetween(4, 5),
            'is_approved' => true,
        ];
    }
}
