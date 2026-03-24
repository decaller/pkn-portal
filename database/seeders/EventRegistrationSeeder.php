<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\Organization;
use App\Models\RegistrationParticipant;
use App\Models\User;
use Illuminate\Database\Seeder;

class EventRegistrationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $events = Event::where('allow_registration', true)->get();
        $users = User::all();
        $organizations = Organization::all();

        if ($events->isEmpty() || $users->isEmpty()) {
            return;
        }

        foreach ($events as $event) {
            // Create 3-5 registrations for each event
            $numRegistrations = rand(3, 5);

            for ($i = 0; $i < $numRegistrations; $i++) {
                $booker = $users->random();
                $organization = $organizations->isNotEmpty() ? $organizations->random() : null;

                $packages = $event->registration_packages ?? [['name' => 'Regular', 'price' => 50000]];
                $selectedPackage = $packages[array_rand($packages)];

                $numParticipants = rand(1, 3);
                $unitPrice = $selectedPackage['price'] ?? 50000;
                $totalAmount = $unitPrice * $numParticipants;

                $registration = EventRegistration::factory()->create([
                    'event_id' => $event->id,
                    'booker_user_id' => $booker->id,
                    'organization_id' => $organization?->id,
                    'total_amount' => $totalAmount,
                    'package_breakdown' => [
                        [
                            'package_name' => $selectedPackage['name'] ?? 'Regular',
                            'participant_count' => $numParticipants,
                            'unit_price' => $unitPrice,
                        ],
                    ],
                ]);

                // Create participants for this registration
                RegistrationParticipant::factory()->count($numParticipants)->create([
                    'registration_id' => $registration->id,
                ]);
            }
        }
    }
}
