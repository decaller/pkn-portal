<?php

namespace App\Services;

use App\Models\Event;

class EventSessionFileService
{
    /**
     * @return array<int, array{file_path: string, session_title: string, session_slug: string|null}>
     */
    public function entries(Event $event): array
    {
        $entries = [];

        foreach ($event->rundown ?? [] as $session) {
            $data = $session['data'] ?? $session;
            $files = $data['session_files'] ?? [];

            foreach ($files as $filePath) {
                if (! is_string($filePath) || $filePath === '') {
                    continue;
                }

                $entries[$filePath] = [
                    'file_path' => $filePath,
                    'session_title' => (string) ($data['title'] ?? 'Untitled'),
                    'session_slug' => isset($data['slug']) && $data['slug'] !== ''
                        ? (string) $data['slug']
                        : null,
                ];
            }
        }

        return array_values($entries);
    }

    /**
     * @return array<int, string>
     */
    public function paths(Event $event): array
    {
        return array_values(array_map(
            static fn (array $entry): string => $entry['file_path'],
            $this->entries($event),
        ));
    }
}
