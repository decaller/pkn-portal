<?php

use App\Models\Organization;
use App\Models\User;
use Database\Seeders\ApiDocsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use ZPMLabs\FilamentApiDocsBuilder\Models\ApiDocs;

uses(RefreshDatabase::class);

it('has correctly formatted api documentation records', function () {
    // Run seeder
    $this->seed(ApiDocsSeeder::class);

    $discoveryDoc = ApiDocs::where('slug', 'like', 'discovery-api-%')->first();

    expect($discoveryDoc)->not->toBeNull();

    // Ensure it's an array (Eloquent cast should handle this)
    $data = $discoveryDoc->data;
    if (is_string($data)) {
        $data = json_decode($data, true);
    }

    expect($data)->toBeArray()
        ->and($data[0])->toHaveKeys(['details', 'instructions', 'request_code', 'response']);

    expect($data[0]['details']['title'])->toBe('Mobile Dashboard');
});

it('can access the api docs in the admin panel', function () {
    $this->seed(ApiDocsSeeder::class);
    $user = User::factory()->create(['is_super_admin' => true]);
    $org = Organization::factory()->create();
    $user->organizations()->attach($org);

    $doc = ApiDocs::first();

    $this->actingAs($user)
        ->get("/admin/{$org->slug}/custom-api-docs/{$doc->id}")
        ->assertOk();
});
