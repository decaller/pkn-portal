<?php

use App\Filament\Admin\Resources\Documents\Pages\CreateDocument;
use App\Models\Document;
use App\Models\Organization;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
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

it('can create a document without a title', function () {
    Livewire::test(CreateDocument::class, [
        'tenant' => $this->tenant->slug,
    ])
        ->fillForm([
            'file_path' => ['manual-uploads/test-file.pdf'],
            'title' => null, // Title is NOT required
            'tags' => ['featured'],
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas(Document::class, [
        'file_path' => 'manual-uploads/test-file.pdf',
        'title' => null,
    ]);

    $document = Document::where('file_path', 'manual-uploads/test-file.pdf')->first();
    expect($document->slug)->toStartWith('doc-');
});

it('can create a document with a cover image', function () {
    Livewire::test(CreateDocument::class, [
        'tenant' => $this->tenant->slug,
    ])
        ->fillForm([
            'file_path' => ['manual-uploads/test-file-2.pdf'],
            'title' => 'Document with cover',
            'cover_image' => ['document-covers/cover.jpg'],
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas(Document::class, [
        'file_path' => 'manual-uploads/test-file-2.pdf',
        'cover_image' => 'document-covers/cover.jpg',
    ]);
});

it('can render the document view page', function () {
    $document = Document::query()->create([
        'title' => 'Test Document',
        'file_path' => 'manual-uploads/test.pdf',
        'metadata' => ['Author' => ['John', 'Jane'], 'Subject' => 'Test'], // Restore nested array
        'tags' => ['test'],
        'slug' => 'test-document',
    ]);

    Livewire::test(\App\Filament\Admin\Resources\Documents\Pages\ViewDocument::class, [
        'record' => $document->getKey(),
        'tenant' => $this->tenant->slug,
    ])
        ->assertSuccessful();
});
