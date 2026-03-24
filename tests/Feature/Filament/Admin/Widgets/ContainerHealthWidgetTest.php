<?php

namespace Tests\Feature\Filament\Admin\Widgets;

use App\Filament\Admin\Widgets\ContainerHealthWidget;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ContainerHealthWidgetTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $user = User::factory()->create([
            'email' => 'admin@pkn.id',
            'is_super_admin' => true,
        ]);

        $this->actingAs($user);
    }

    public function test_it_can_render_the_health_widget()
    {
        Livewire::test(ContainerHealthWidget::class)
            ->assertStatus(200)
            ->assertSee(__('Stack Health Monitoring'));
    }

    public function test_it_lists_all_containers()
    {
        Livewire::test(ContainerHealthWidget::class)
            ->assertStatus(200)
            ->assertSee(__('App (laravel.test)'))
            ->assertSee(__('Worker (laravel.worker)'))
            ->assertSee(__('Database (PostgreSQL)'))
            ->assertSee(__('Cache (Redis)'))
            ->assertSee(__('Search (Meilisearch)'))
            ->assertSee(__('Fulltext (Tika)'))
            ->assertSee(__('Office (Collabora)'));
    }

    public function test_it_shows_refresh_button()
    {
        Livewire::test(ContainerHealthWidget::class)
            ->assertStatus(200)
            ->assertSee(__('Refresh Status'));
    }

    public function test_it_can_call_refresh_action()
    {
        Livewire::test(ContainerHealthWidget::class)
            ->callTableAction('refresh')
            ->assertNotified(__('Health status updated'));
    }
}
