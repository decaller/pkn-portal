<?php

use App\Console\Commands\SendParticipantSlotRemindersCommand;
use App\Console\Commands\SendPaymentReminderNotificationsCommand;
use App\Enums\PaymentStatus;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\Organization;
use App\Models\User;
use App\Notifications\EmptyParticipantSpotReminderNotification;
use App\Notifications\NewEventOpenForRegistrationNotification;
use App\Notifications\PastEventPostedOrUpdatedNotification;
use App\Notifications\PaymentApprovedNotification;
use App\Notifications\PaymentUploadReminderNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

uses(RefreshDatabase::class);

// ─── PaymentUploadReminder: sent when registration is created ───

it('sends a payment upload reminder when a registration is created', function () {
    Notification::fake();

    $user = User::factory()->create();
    $organization = Organization::factory()->create(['admin_user_id' => $user->id]);
    $user->organizations()->attach($organization, ['role' => 'admin']);
    $event = Event::factory()->create(['allow_registration' => true, 'event_date' => now()->addDays(10)]);

    EventRegistration::create([
        'event_id' => $event->id,
        'organization_id' => $organization->id,
        'booker_user_id' => $user->id,
        'package_breakdown' => [['package_name' => 'General', 'quantity' => 1, 'unit_price' => 0]],
        'total_amount' => 0,
        'status' => \App\Enums\RegistrationStatus::Draft,
        'payment_status' => PaymentStatus::Unpaid,
    ]);

    Notification::assertSentTo($user, PaymentUploadReminderNotification::class);
});

// ─── PaymentApproved: sent when payment_status changes to Verified ───

it('sends a payment approved notification when payment status changes to Verified', function () {
    Notification::fake();

    $admin = User::factory()->create(['is_super_admin' => true]);
    $user = User::factory()->create();
    $organization = Organization::factory()->create(['admin_user_id' => $user->id]);
    $user->organizations()->attach($organization, ['role' => 'admin']);
    $event = Event::factory()->create(['allow_registration' => true, 'event_date' => now()->addDays(10)]);

    $registration = EventRegistration::create([
        'event_id' => $event->id,
        'organization_id' => $organization->id,
        'booker_user_id' => $user->id,
        'package_breakdown' => [['package_name' => 'General', 'quantity' => 1, 'unit_price' => 0]],
        'total_amount' => 0,
        'status' => \App\Enums\RegistrationStatus::Draft,
        'payment_status' => PaymentStatus::Unpaid,
    ]);

    Notification::fake(); // Reset — only care about payment approved

    $registration->verifyPayment($admin);

    Notification::assertSentTo($user, PaymentApprovedNotification::class);
});

// ─── NewEventOpenForRegistration: sent when allow_registration is toggled true ───

it('sends a new event open notification to all org users when registration is enabled', function () {
    Notification::fake();

    $user = User::factory()->create();
    $organization = Organization::factory()->create(['admin_user_id' => $user->id]);
    $user->organizations()->attach($organization, ['role' => 'admin']);

    $event = Event::factory()->create(['allow_registration' => false, 'event_date' => now()->addDays(10)]);

    $event->update(['allow_registration' => true]);

    Notification::assertSentTo($user, NewEventOpenForRegistrationNotification::class);
});

// ─── SendPaymentReminderNotificationsCommand ───

it('sends payment reminders via the scheduled command', function () {
    Notification::fake();

    $user = User::factory()->create();
    $organization = Organization::factory()->create(['admin_user_id' => $user->id]);
    $user->organizations()->attach($organization, ['role' => 'admin']);
    $event = Event::factory()->create(['allow_registration' => true, 'event_date' => now()->addDays(10)]);

    EventRegistration::create([
        'event_id' => $event->id,
        'organization_id' => $organization->id,
        'booker_user_id' => $user->id,
        'package_breakdown' => [['package_name' => 'General', 'quantity' => 1, 'unit_price' => 0]],
        'total_amount' => 0,
        'status' => \App\Enums\RegistrationStatus::Draft,
        'payment_status' => PaymentStatus::Unpaid,
    ]);

    Notification::fake(); // Reset after registration creation notification

    $this->artisan(SendPaymentReminderNotificationsCommand::class)
        ->assertExitCode(0);

    Notification::assertSentTo($user, PaymentUploadReminderNotification::class);
});

// ─── SendParticipantSlotRemindersCommand ───

it('sends participant slot reminders via the scheduled command when spots are empty', function () {
    Notification::fake();

    $user = User::factory()->create();
    $organization = Organization::factory()->create(['admin_user_id' => $user->id]);
    $user->organizations()->attach($organization, ['role' => 'admin']);
    $event = Event::factory()->create(['allow_registration' => true, 'event_date' => now()->addDays(10)]);

    // Create registration with 2 expected participants, but no actual participants
    EventRegistration::create([
        'event_id' => $event->id,
        'organization_id' => $organization->id,
        'booker_user_id' => $user->id,
        'package_breakdown' => [['package_name' => 'General', 'quantity' => 2, 'unit_price' => 0]],
        'total_amount' => 0,
        'status' => \App\Enums\RegistrationStatus::Draft,
        'payment_status' => PaymentStatus::Unpaid,
    ]);

    Notification::fake(); // Reset after creation

    $this->artisan(SendParticipantSlotRemindersCommand::class)
        ->assertExitCode(0);

    Notification::assertSentTo($user, EmptyParticipantSpotReminderNotification::class);
});

// ─── PastEventPostedOrUpdated: sent when a past event is published ───

it('sends a past event notification when an event with a past date is published', function () {
    Notification::fake();

    $user = User::factory()->create();
    $organization = Organization::factory()->create(['admin_user_id' => $user->id]);
    $user->organizations()->attach($organization, ['role' => 'admin']);

    $event = Event::factory()->create([
        'is_published' => false,
        'allow_registration' => false,
        'event_date' => now()->subDays(30),
    ]);

    $event->update(['is_published' => true]);

    Notification::assertSentTo($user, PastEventPostedOrUpdatedNotification::class);
});
