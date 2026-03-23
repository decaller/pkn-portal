<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Laravel\Sanctum\Sanctum;

class UpdateApiResults extends Command
{
    protected $signature = 'api:update-results';

    protected $description = 'Update target folder with updated API result JSON files';

    public function handle()
    {
        $user = User::first();

        // 1. Mobile Dashboard
        $this->saveResponse('/api/v1/mobile-dashboard', 'mobile-dashboard.json');

        // 2. Events List & Detail
        $events = $this->saveResponse('/api/v1/events', 'events.json');
        if (! empty($events['data'])) {
            $eventId = $events['data'][0]['id'];
            $this->saveResponse("/api/v1/events/$eventId", "events/$eventId.json");
            $this->saveResponse("/api/v1/events/$eventId/similar", "events/$eventId/similar.json");
        }

        // 3. News List & Detail
        $news = $this->saveResponse('/api/v1/news', 'news.json');
        if (! empty($news['data'])) {
            $newsId = $news['data'][0]['id'];
            $this->saveResponse("/api/v1/news/$newsId", "news/$newsId.json");
        }

        // 4. Documents List
        $this->saveResponse('/api/v1/documents', 'documents.json', $user);

        // 5. Authenticated endpoints
        if ($user) {
            $this->info("Using user: {$user->name} (ID: {$user->id})");
            $this->saveResponse('/api/v1/user/profile', 'profile.json', $user);
            $this->saveResponse('/api/v1/notifications', 'notifications.json', $user);
            $this->saveResponse('/api/v1/organizations', 'organizations.json', $user);

            $registrations = $this->saveResponse('/api/v1/registrations', 'registrations.json', $user);
            if (! empty($registrations['data'])) {
                $regId = $registrations['data'][0]['id'];
                $this->saveResponse("/api/v1/registrations/$regId", "registrations/$regId.json", $user);
                $this->saveResponse("/api/v1/registrations/$regId/participants", "registrations/$regId/participants.json", $user);
            }

            $invoices = $this->saveResponse('/api/v1/invoices', 'invoices.json', $user);
            if (! empty($invoices['data'])) {
                $invId = $invoices['data'][0]['id'];
                $this->saveResponse("/api/v1/invoices/$invId", "invoices/$invId.json", $user);
                $this->saveResponse("/api/v1/invoices/$invId/download", "invoices/$invId/download.json", $user);
            }
        } else {
            $this->warn('No user found for authenticated endpoints.');
        }

        $this->info('Alhamdulillah, all specified API results updated.');
    }

    protected function saveResponse($path, $filename, $user = null)
    {
        if ($user) {
            Sanctum::actingAs($user);
        }

        $request = Request::create($path, 'GET');
        $response = app()->handle($request);
        $content = $response->getContent();

        $json = json_decode($content, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->error("Error decoding JSON for $path: ".json_last_error_msg());
            $this->line('Response: '.substr($content, 0, 200));

            return null;
        }

        $prettyJson = json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        $targetPath = base_path('react native dev guide/api_result/'.$filename);

        // Ensure directory exists
        $dir = dirname($targetPath);
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        file_put_contents($targetPath, $prettyJson);
        $this->line("Updated: $filename");

        return $json;
    }
}
