<?php

use App\Models\Document;
use App\Models\Event;
use App\Models\News;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('guest can access mobile dashboard', function () {
    Event::factory()->count(3)->create(['is_published' => true]);
    News::factory()->count(2)->create(['is_published' => true]);

    $response = $this->getJson('/api/v1/mobile-dashboard');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'featured_events' => [
                '*' => ['id', 'title', 'slug', 'cover_image'],
            ],
            'latest_news' => [
                '*' => ['id', 'title', 'thumbnail'],
            ],
            'testimonials' => [],
        ]);
});

test('guest can list and search events', function () {
    Event::factory()->create(['title' => 'Searchable Event', 'is_published' => true]);
    Event::factory()->create(['title' => 'Other Event', 'is_published' => true]);

    $response = $this->getJson('/api/v1/events?search=Searchable');

    $response->assertStatus(200)
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.title', 'Searchable Event');
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
        ->assertJsonCount(5, 'data');
});
