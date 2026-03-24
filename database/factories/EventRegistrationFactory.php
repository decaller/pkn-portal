<?php

namespace Database\Factories;

use App\Enums\PaymentStatus;
use App\Enums\RegistrationStatus;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<EventRegistration>
 */
class EventRegistrationFactory extends Factory
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
            'booker_user_id' => User::factory(),
            'status' => $this->faker->randomElement(RegistrationStatus::cases()),
            'payment_status' => $this->faker->randomElement(PaymentStatus::cases()),
            'total_amount' => $this->faker->randomElement([50000, 100000, 250000, 500000]),
            'package_breakdown' => [
                [
                    'package_name' => 'Regular',
                    'participant_count' => 1,
                    'unit_price' => 50000,
                ],
            ],
            'notes' => $this->faker->optional()->sentence(),
        ];
    }
}
