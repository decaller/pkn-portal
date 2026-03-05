<?php

use App\Enums\PaymentStatus;
use App\Enums\RegistrationStatus;
use App\Filament\Admin\Resources\EventRegistrations\Pages\EditEventRegistration;
use App\Filament\Admin\Resources\News\NewsResource;
use App\Filament\Admin\Resources\News\Pages\CreateNews;
use App\Filament\Admin\Resources\News\Pages\EditNews;
use App\Filament\Admin\Resources\News\Pages\ListNews;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\News;
use App\Models\Organization;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->admin = User::factory()->create([
        'is_super_admin' => true,
    ]);

    $this->tenant = Organization::create([
        'name' => 'Admin Tenant',
        'slug' => 'admin-tenant',
        'admin_user_id' => $this->admin->getKey(),
    ]);

    $this->tenant->users()->syncWithoutDetaching([
        $this->admin->getKey() => ['role' => 'admin'],
    ]);

    $this->actingAs($this->admin);

    Filament::setCurrentPanel(Filament::getPanel('admin'));
    Filament::setTenant($this->tenant);
});

function createEvent(array $attributes = []): Event
{
    return Event::query()->create(array_merge([
        'title' => 'Event '.Str::random(6),
        'slug' => 'event-'.Str::random(8),
        'description' => 'Event description',
        'event_date' => now()->addWeek()->toDateString(),
        'is_published' => true,
        'allow_registration' => false,
        'event_type' => 'offline',
    ], $attributes));
}

function createNews(array $attributes = []): News
{
    return News::query()->create(array_merge([
        'title' => 'News '.Str::random(6),
        'content' => 'News content',
        'is_published' => true,
    ], $attributes));
}

it('renders the admin news resource index page', function (): void {
    $this->get(NewsResource::getUrl('index'))
        ->assertSuccessful();
});

it('tests the news table records and search', function (): void {
    $firstNews = createNews(['title' => 'Alpha News']);
    $secondNews = createNews(['title' => 'Beta News']);

    Livewire::test(ListNews::class)
        ->assertCanSeeTableRecords([$firstNews, $secondNews])
        ->assertCountTableRecords(2)
        ->assertTableColumnExists('title')
        ->assertTableColumnExists('created_at');
});

it('tests the create news resource schema and creation flow', function (): void {
    $event = createEvent(['title' => 'Linked Event']);

    Livewire::test(CreateNews::class)
        ->assertSchemaExists('form')
        ->assertSchemaComponentExists('title')
        ->assertSchemaComponentExists('content')
        ->assertSchemaComponentExists('is_published')
        ->fillForm([
            'title' => 'Filament Testing News',
            'content' => 'Created from Filament resource test.',
            'is_published' => true,
            'event_id' => $event->getKey(),
        ])
        ->assertSchemaStateSet([
            'title' => 'Filament Testing News',
            'event_id' => $event->getKey(),
        ])
        ->call('create')
        ->assertHasNoFormErrors()
        ->assertNotified();

    $this->assertDatabaseHas(News::class, [
        'title' => 'Filament Testing News',
        'event_id' => $event->getKey(),
    ]);
});

it('tests editing a news resource record', function (): void {
    $news = createNews([
        'title' => 'Initial Title',
        'content' => 'Initial Content',
    ]);

    Livewire::test(EditNews::class, ['record' => $news->getRouteKey()])
        ->assertSchemaStateSet([
            'title' => 'Initial Title',
        ])
        ->fillForm([
            'title' => 'Updated Title',
            'content' => 'Updated Content',
            'is_published' => false,
        ])
        ->call('save')
        ->assertHasNoFormErrors()
        ->assertNotified();

    $news->refresh();

    expect($news->title)->toBe('Updated Title')
        ->and($news->is_published)->toBeFalse();
});

it('tests a custom filament action and notification on event registration edit', function (): void {
    $event = createEvent();
    $booker = User::factory()->create();

    $registration = EventRegistration::query()->create([
        'event_id' => $event->getKey(),
        'organization_id' => $this->tenant->getKey(),
        'booker_user_id' => $booker->getKey(),
        'status' => RegistrationStatus::PendingPayment->value,
        'payment_status' => PaymentStatus::Submitted->value,
        'total_amount' => 250000,
    ]);

    Livewire::test(EditEventRegistration::class, ['record' => $registration->getRouteKey()])
        ->assertActionExists('verify_payment')
        ->callAction('verify_payment')
        ->assertNotified(__('Payment Verified'));

    $registration->refresh();

    expect($registration->payment_status)->toBe(PaymentStatus::Verified)
        ->and($registration->status)->toBe(RegistrationStatus::Paid)
        ->and($registration->verified_by_user_id)->toBe($this->admin->getKey())
        ->and($registration->verified_at)->not->toBeNull();
});
