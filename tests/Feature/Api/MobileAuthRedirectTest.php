<?php

use App\Filament\User\Auth\Login;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

it('redirects to token handoff when logging in with mobile source via Livewire', function () {
    $user = User::factory()->create([
        'password' => bcrypt('password'),
    ]);

    $organization = Organization::factory()->create([
        'admin_user_id' => $user->id,
    ]);
    $organization->users()->attach($user, ['role' => 'admin']);

    // session flag set via mount() in Login page if source=mobile is in URL
    Livewire::withQueryParams(['source' => 'mobile'])
        ->test(Login::class)
        ->fillForm([
            'phone_number' => $user->phone_number,
            'password' => 'password',
        ])
        ->call('authenticate')
        ->assertRedirect(route('api.v1.auth.token-handoff'));
});

it('redirects to dashboard by default without mobile source via Livewire', function () {
    $user = User::factory()->create([
        'password' => bcrypt('password'),
    ]);

    $organization = Organization::factory()->create([
        'admin_user_id' => $user->id,
    ]);
    $organization->users()->attach($user, ['role' => 'admin']);

    Livewire::test(Login::class)
        ->fillForm([
            'phone_number' => $user->phone_number,
            'password' => 'password',
        ])
        ->call('authenticate')
        ->assertRedirect(route('filament.admin.pages.dashboard', ['tenant' => $organization]));
});

it('hides sidebar and topbar when mobile source is active in session', function () {
    $user = User::factory()->create();
    $organization = Organization::factory()->create(['admin_user_id' => $user->id]);
    $organization->users()->attach($user, ['role' => 'admin']);

    $this->actingAs($user)
        ->withSession(['mobile_source' => 'mobile'])
        ->get(route('filament.admin.pages.dashboard', ['tenant' => $organization]))
        ->assertOk()
        ->assertSee('.fi-sidebar', false) // Check if CSS is present
        ->assertSee('display: none !important', false);
});
