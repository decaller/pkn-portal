<?php

use App\Enums\PaymentStatus;
use App\Enums\RegistrationStatus;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\RegistrationParticipant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\getJson;

uses(RefreshDatabase::class);

it('returns registration details in the correct JSON shape matching 9.json', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $event = Event::factory()->create([
        'title' => 'PKN National Conference 2026',
        'slug' => 'pkn-national-conference-2026',
        'registration_packages' => [
            ['name' => 'Regular', 'price' => 100000, 'max_quota' => null],
            ['name' => 'VIP', 'price' => 250000, 'max_quota' => null],
        ],
    ]);

    $registration = EventRegistration::create([
        'event_id' => $event->id,
        'booker_user_id' => $user->id,
        'status' => RegistrationStatus::Draft,
        'payment_status' => PaymentStatus::Unpaid,
        'total_amount' => 100000,
        'package_breakdown' => [
            [
                'package_name' => 'Regular',
                'participant_count' => 1,
                'unit_price' => 100000,
                'debug_log' => 'Total amount synced: 100000',
            ],
        ],
    ]);

    RegistrationParticipant::create([
        'registration_id' => $registration->id,
        'name' => 'Participant 1',
        'category' => 'Adult',
    ]);

    $response = getJson("/api/v1/registrations/{$registration->id}");

    $response->assertStatus(200);

    assertMatchesApiResult($response, 'registrations/9.json');
});
