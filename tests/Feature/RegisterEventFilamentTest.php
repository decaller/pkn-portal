<?php

use App\Enums\PaymentStatus;
use App\Enums\RegistrationStatus;
use App\Filament\User\Auth\RegisterEvent;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Need to set up the panel context because RegisterEvent is a panel page.
    $panel = Filament::getPanel('user');
    Filament::setCurrentPanel($panel);
});

it('can register a personal user and create an event registration', function () {
    $event = Event::factory()->create([
        'is_published' => true,
        'allow_registration' => true,
        'event_date' => now()->addDays(5),
        'registration_packages' => [
            [
                'id' => 'general',
                'name' => 'General',
                'price' => 0,
            ],
        ],
    ]);

    Livewire::test(RegisterEvent::class)
        ->fillForm([
            'name' => 'John Doe',
            'phone_number' => '081234567890',
            'registration_type' => 'personal',
            'password' => 'password123',
            'passwordConfirmation' => 'password123',
            'is_registering_for_event' => true,
            'event_id' => $event->id,
            'package_breakdown' => [
                [
                    'package_name' => 'General',
                    'participant_count' => 1,
                    'unit_price' => 0,
                ],
            ],
            'total_amount' => 0,
        ])
        ->call('register')
        ->assertHasNoFormErrors();

    // Verify user is created
    $user = User::where('phone_number', '081234567890')->first();
    expect($user)->not->toBeNull()
        ->and($user->name)->toBe('John Doe')
        ->and(Hash::check('password123', $user->password))->toBeTrue();

    // Verify event registration is created
    $registration = EventRegistration::where('booker_user_id', $user->id)
        ->where('event_id', $event->id)
        ->first();

    expect($registration)->not->toBeNull()
        ->and($registration->status)->toBe(RegistrationStatus::Paid)
        ->and($registration->payment_status)->toBe(PaymentStatus::Verified)
        ->and($registration->total_amount)->toEqual(0);

    // Verify Personal Organization is created
    expect($user->organizations)->toHaveCount(1);
    $org = $user->organizations->first();
    expect($org->name)->toBe('Personal - John Doe')
        ->and($org->slug)->toStartWith('personal-john-doe-');

    // Due to how Livewire testing works for Panel redirects, we verify the user is tied to the org
    // successfully, matching what `getRedirectUrl()` expects.
});

it('prevents registration with an already registered phone number', function () {
    $existingUser = User::factory()->create([
        'phone_number' => '08999999999',
    ]);

    Livewire::test(RegisterEvent::class)
        ->fillForm([
            'name' => 'Jane Doe',
            'phone_number' => '08999999999', // Same as existing
            'registration_type' => 'personal',
            'password' => 'password123',
            'is_registering_for_event' => false,
        ])
        ->call('register')
        ->assertHasFormErrors(['phone_number' => 'unique']);
});
