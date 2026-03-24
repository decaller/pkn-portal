<?php

namespace App\Filament\Admin\Widgets;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Throwable;

class ContainerHealthWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading(__('Stack Health Monitoring'))
            ->description(__('Real-time status of the application stack containers'))
            ->poll('10s')
            ->records(fn () => $this->getTableRecords())
            ->columns([
                TextColumn::make('service')
                    ->label(__('Service'))
                    ->weight('bold')
                    ->icon(fn ($record) => $record['icon']),
                IconColumn::make('healthy')
                    ->label(__('Status'))
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
                TextColumn::make('description')
                    ->label(__('Description'))
                    ->size('sm')
                    ->color('gray'),
            ])
            ->headerActions([
                Action::make('refresh')
                    ->label(__('Refresh Status'))
                    ->color('gray')
                    ->icon('heroicon-m-arrow-path')
                    ->action(fn () => Notification::make()->title(__('Health status updated'))->success()->send()),
            ])
            ->recordActions([
                Action::make('restart')
                    ->label(__('Restart'))
                    ->icon('heroicon-m-arrow-path')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->action(fn ($record) => $this->restartHandler($record['id'])),
            ])
            ->paginated(false);
    }

    /**
     * Override to provide static health data as records.
     * In Filament v5, array records must have a '__key' entry.
     */
    public function getTableRecords(): Collection
    {
        return collect([
            'laravel.test' => array_merge($this->checkAppHealth(), ['__key' => 'laravel.test']),
            'laravel.worker' => array_merge($this->checkWorkerHealth(), ['__key' => 'laravel.worker']),
            'pgsql' => array_merge($this->checkDatabaseHealth(), ['__key' => 'pgsql']),
            'redis' => array_merge($this->checkRedisHealth(), ['__key' => 'redis']),
            'meilisearch' => array_merge($this->checkMeilisearchHealth(), ['__key' => 'meilisearch']),
            'tika' => array_merge($this->checkTikaHealth(), ['__key' => 'tika']),
            'libreoffice' => array_merge($this->checkLibreOfficeHealth(), ['__key' => 'libreoffice']),
        ]);
    }

    protected function checkAppHealth(): array
    {
        return [
            'id' => 'laravel.test',
            'service' => __('App (laravel.test)'),
            'icon' => 'heroicon-o-cpu-chip',
            'healthy' => true,
            'description' => __('Main application container'),
        ];
    }

    protected function checkWorkerHealth(): array
    {
        $status = $this->getContainerStatus('laravel.worker');

        return [
            'id' => 'laravel.worker',
            'service' => __('Worker (laravel.worker)'),
            'icon' => 'heroicon-o-queue-list',
            'healthy' => $status,
            'description' => $status ? __('Container is running') : __('Container not running'),
        ];
    }

    protected function checkDatabaseHealth(): array
    {
        try {
            DB::connection()->getPdo();
            $status = true;
        } catch (Throwable) {
            $status = false;
        }

        return [
            'id' => 'pgsql',
            'service' => __('Database (PostgreSQL)'),
            'icon' => 'heroicon-o-circle-stack',
            'healthy' => $status,
            'description' => $status ? __('Connected and responding') : __('Connection failed'),
        ];
    }

    protected function checkRedisHealth(): array
    {
        $status = $this->getContainerStatus('redis');

        return [
            'id' => 'redis',
            'service' => __('Cache (Redis)'),
            'icon' => 'heroicon-o-bolt',
            'healthy' => $status,
            'description' => $status ? __('Container is running') : __('Container not running'),
        ];
    }

    protected function checkMeilisearchHealth(): array
    {
        try {
            $response = Http::timeout(2)->get('http://meilisearch:7700/health');
            $status = $response->successful() && ($response->json('status') === 'available' || $response->json('status') === 'healthy');
        } catch (Throwable) {
            $status = false;
        }

        return [
            'id' => 'meilisearch',
            'service' => __('Search (Meilisearch)'),
            'icon' => 'heroicon-o-magnifying-glass',
            'healthy' => $status,
            'description' => $status ? __('Engine available') : __('Engine unreachable'),
        ];
    }

    protected function checkTikaHealth(): array
    {
        try {
            $response = Http::timeout(2)->get('http://tika:9998/version');
            $status = $response->successful();
        } catch (Throwable) {
            $status = false;
        }

        return [
            'id' => 'tika',
            'service' => __('Fulltext (Tika)'),
            'icon' => 'heroicon-o-document-magnifying-glass',
            'healthy' => $status,
            'description' => $status ? __('Extraction active') : __('Service unreachable'),
        ];
    }

    protected function checkLibreOfficeHealth(): array
    {
        try {
            $response = Http::timeout(2)->get('http://libreoffice:9980');
            $status = $response->successful() || $response->status() === 404;
        } catch (Throwable) {
            $status = false;
        }

        return [
            'id' => 'libreoffice',
            'service' => __('Office (Collabora)'),
            'icon' => 'heroicon-o-document-text',
            'healthy' => $status,
            'description' => $status ? __('Previewer active') : __('Service unreachable'),
        ];
    }

    protected function restartHandler(string $id): void
    {
        if ($id === 'laravel.worker') {
            Artisan::call('queue:restart');
            Notification::make()->title(__('Worker restart signal sent'))->success()->send();

            return;
        }

        $this->restartContainer($id);
    }

    protected function getContainerStatus(string $serviceName): bool
    {
        try {
            $filters = json_encode(['label' => ["com.docker.compose.service=$serviceName"]]);
            $output = shell_exec('curl --unix-socket /var/run/docker.sock -s "http://localhost/v1.41/containers/json?filters='.urlencode($filters).'"');

            $containers = json_decode($output, true);

            if (empty($containers)) {
                $output = shell_exec('curl --unix-socket /var/run/docker.sock -s "http://localhost/v1.41/containers/json?filters='.urlencode(json_encode(['name' => [$serviceName]])).'"');
                $containers = json_decode($output, true);
            }

            if (! empty($containers) && isset($containers[0]['State'])) {
                return $containers[0]['State'] === 'running';
            }
        } catch (Throwable) {
            // Ignore format/exec errors and return false
        }

        return false;
    }

    protected function restartContainer(string $serviceName): void
    {
        try {
            $filters = json_encode(['label' => ["com.docker.compose.service=$serviceName"]]);
            $output = shell_exec('curl --unix-socket /var/run/docker.sock -s "http://localhost/v1.41/containers/json?filters='.urlencode($filters).'"');

            $containers = json_decode($output, true);

            if (empty($containers)) {
                $output = shell_exec('curl --unix-socket /var/run/docker.sock -s "http://localhost/v1.41/containers/json?filters='.urlencode(json_encode(['name' => [$serviceName]])).'"');
                $containers = json_decode($output, true);
            }

            if (! empty($containers) && isset($containers[0]['Id'])) {
                $id = $containers[0]['Id'];
                shell_exec("curl --unix-socket /var/run/docker.sock -X POST \"http://localhost/v1.41/containers/$id/restart\"");

                Notification::make()
                    ->title(__('Restart command sent for :service', ['service' => $serviceName]))
                    ->success()
                    ->send();
            } else {
                throw new \Exception(__('Container not found for service: :service', ['service' => $serviceName]));
            }
        } catch (Throwable $e) {
            Notification::make()
                ->title(__('Failed to restart :service', ['service' => $serviceName]))
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }
}
