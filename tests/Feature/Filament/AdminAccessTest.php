<?php

use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->superAdmin = User::factory()->create([
        'is_super_admin' => true,
    ]);

    $this->normalUser = User::factory()->create([
        'is_super_admin' => false,
    ]);

    $this->tenant = Organization::create([
        'name' => 'Test Organization',
        'slug' => 'test-org',
        'admin_user_id' => $this->normalUser->getKey(),
    ]);

    $this->tenant->users()->attach($this->normalUser->getKey(), ['role' => 'admin']);
});

it('allows super admin to access the admin panel', function () {
    $this->actingAs($this->superAdmin);

    // Use the tenant-aware URL for the admin dashboard
    $url = "/admin/{$this->tenant->slug}";

    $this->get($url)
        ->assertSuccessful();
});

it('redirects normal user from admin panel to user panel', function () {
    $this->actingAs($this->normalUser);

    $url = "/admin/{$this->tenant->slug}";

    $this->get($url)
        ->assertRedirect('/user');
});

it('redirects guest from admin panel to admin login page', function () {
    $url = "/admin/{$this->tenant->slug}";

    $this->get($url)
        ->assertRedirect('/admin/login');
});
