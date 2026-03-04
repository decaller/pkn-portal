<?php

use App\Filament\Pages\Tenancy\RegisterOrganization;
use App\Models\Organization;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $panel = Filament::getPanel('user');
    Filament::setCurrentPanel($panel);
});

it('can join an existing organization', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $existingOrg = Organization::factory()->create([
        'name' => 'Existing Org',
        'slug' => 'existing-org',
    ]);

    Livewire::test(RegisterOrganization::class)
        ->fillForm([
            'registration_type' => 'existing',
            'existing_organization_id' => $existingOrg->id,
        ])
        ->call('register')
        ->assertHasNoFormErrors();

    // Verify user is attached
    expect($existingOrg->users->contains($user->id))->toBeTrue();
    // Verify they are a user, not an admin
    $role = $existingOrg->users()->where('user_id', $user->id)->first()->pivot->role;
    expect($role)->toBe('user');
});

it('can create a new organization', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    Livewire::test(RegisterOrganization::class)
        ->fillForm([
            'registration_type' => 'new',
            'name' => 'New Org',
            'slug' => 'new-org',
        ])
        ->call('register')
        ->assertHasNoFormErrors();

    $newOrg = Organization::where('slug', 'new-org')->first();
    expect($newOrg)->not->toBeNull()
        ->and($newOrg->admin_user_id)->toBe($user->id);

    // Verify user is attached as admin
    expect($newOrg->users->contains($user->id))->toBeTrue();
    $role = $newOrg->users()->where('user_id', $user->id)->first()->pivot->role;
    expect($role)->toBe('admin');
});
