<?php

use App\Enums\EventType;
use App\Models\Document;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\News;
use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

test('guest can access mobile dashboard', function () {
    Event::factory()->count(3)->create(['is_published' => true]);
    News::factory()->count(2)->create(['is_published' => true]);
    Testimonial::factory()->count(2)->create(['is_approved' => true]);

    $response = $this->getJson('/api/v1/mobile-dashboard');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'featured_events' => [
                '*' => ['id', 'title', 'slug', 'cover_image'],
            ],
            'latest_news' => [
                '*' => ['id', 'title', 'thumbnail'],
            ],
            'testimonials' => [
                '*' => ['id', 'content', 'rating', 'user' => ['name']],
            ],
            'featured_documents' => [
                '*' => ['id', 'title', 'slug', 'file_url'],
            ],
            'contact_info' => [
                'phone',
                'whatsapp_url',
            ],
            'alerts',
            'stats' => [
                'active_registrations',
                'pending_payments',
            ],
        ]);
});

test('dashboard stats and alerts are scoped to the authenticated mobile user when a bearer token is present', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    EventRegistration::factory()->create([
        'booker_user_id' => $user->id,
        'status' => 'draft',
        'payment_status' => 'unpaid',
    ]);
    EventRegistration::factory()->create([
        'booker_user_id' => $otherUser->id,
        'status' => 'draft',
        'payment_status' => 'submitted',
    ]);

    $user->notify(new class extends \Illuminate\Notifications\Notification
    {
        public function via(object $notifiable): array
        {
            return ['database'];
        }

        public function toDatabase(object $notifiable): array
        {
            return [
                'type' => 'info',
                'title' => 'Pending payment',
                'message' => 'You still have one unpaid registration.',
                'action_route' => '/payments',
            ];
        }
    });

    Sanctum::actingAs($user, ['*']);

    $response = $this->getJson('/api/v1/mobile-dashboard');

    $response->assertSuccessful()
        ->assertJsonPath('stats.active_registrations', 1)
        ->assertJsonPath('stats.pending_payments', 1)
        ->assertJsonPath('alerts.0.title', 'Pending payment');
});

test('guest can list and search events', function () {
    Event::factory()->create(['title' => 'Searchable Event', 'is_published' => true]);
    Event::factory()->create(['title' => 'Other Event', 'is_published' => true]);

    $response = $this->getJson('/api/v1/events?search=Searchable');

    $response->assertStatus(200)
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.title', 'Searchable Event');
});

test('guest can filter events by category', function () {
    $onlineTitle = 'Online Event '.Str::random(5);
    Event::factory()->create(['title' => $onlineTitle, 'event_type' => EventType::Online, 'is_published' => true]);
    Event::factory()->create(['event_type' => EventType::Offline, 'is_published' => true]);

    $response = $this->getJson('/api/v1/events?category=online&search='.$onlineTitle);

    $response->assertStatus(200)
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.title', $onlineTitle);
});

test('guest can filter events by status', function () {
    $openTitle = 'Open Event '.Str::random(5);
    $closedTitle = 'Closed Event '.Str::random(5);
    Event::factory()->create(['title' => $openTitle, 'allow_registration' => true, 'is_published' => true]);
    Event::factory()->create(['title' => $closedTitle, 'allow_registration' => false, 'is_published' => true]);

    $responseOpen = $this->getJson("/api/v1/events?status=open&search={$openTitle}");
    $responseOpen->assertStatus(200)->assertJsonCount(1, 'data');
    $responseOpen->assertJsonPath('data.0.title', $openTitle);

    $responseClosed = $this->getJson("/api/v1/events?status=closed&search={$closedTitle}");
    $responseClosed->assertStatus(200)->assertJsonCount(1, 'data');
    $responseClosed->assertJsonPath('data.0.title', $closedTitle);
});

test('guest can view published event detail', function () {
    $event = Event::factory()->create(['is_published' => true]);

    $response = $this->getJson("/api/v1/events/{$event->id}");

    $response->assertStatus(200)
        ->assertJsonPath('data.title', $event->title);
});

test('guest cannot view unpublished event detail', function () {
    $event = Event::factory()->create(['is_published' => false]);

    $response = $this->getJson("/api/v1/events/{$event->id}");

    $response->assertStatus(404);
});

test('guest can list news with pagination', function () {
    News::factory()->count(20)->create(['is_published' => true]);

    $response = $this->getJson('/api/v1/news');

    $response->assertStatus(200)
        ->assertJsonStructure(['data', 'links', 'meta'])
        ->assertJsonCount(15, 'data'); // default per_page is 15
});

test('guest can list documents', function () {
    Document::factory()->count(5)->create(['is_active' => true]);

    $response = $this->getJson('/api/v1/documents');

    $response->assertStatus(200)
        ->assertJsonCount(5, 'documents.data')
        ->assertJsonStructure([
            'featured',
            'documents' => [
                'data' => [
                    '*' => ['id', 'title', 'is_featured'],
                ],
            ],
        ]);
});

test('guest can filter featured documents', function () {
    Document::factory()->create(['is_active' => true, 'tags' => ['featured'], 'title' => 'Featured Doc']);
    Document::factory()->create(['is_active' => true, 'tags' => ['regular'], 'title' => 'Regular Doc']);

    $response = $this->getJson('/api/v1/documents?is_featured=1');

    $response->assertStatus(200)
        ->assertJsonCount(1, 'featured')
        ->assertJsonPath('featured.0.title', 'Featured Doc');
});

test('guest can filter documents by category tag', function () {
    Document::factory()->create(['is_active' => true, 'tags' => ['guide'], 'title' => 'Guide Doc']);
    Document::factory()->create(['is_active' => true, 'tags' => ['policy'], 'title' => 'Policy Doc']);

    $response = $this->getJson('/api/v1/documents?category=guide');

    $response->assertSuccessful()
        ->assertJsonCount(1, 'documents.data')
        ->assertJsonPath('documents.data.0.title', 'Guide Doc');
});
