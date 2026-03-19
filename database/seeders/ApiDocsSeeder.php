<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use ZPMLabs\FilamentApiDocsBuilder\Models\ApiDocs;

class ApiDocsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Discovery' => [
                [
                    'title' => 'Mobile Dashboard',
                    'route' => '/api/v1/mobile-dashboard',
                    'method' => 'GET',
                    'description' => 'Fetch featured events, news, and testimonials for the home screen.',
                ],
            ],
            'Events' => [
                [
                    'title' => 'List Events',
                    'route' => '/api/v1/events',
                    'method' => 'GET',
                    'description' => 'Paginated list of upcoming events with search and filters.',
                ],
                [
                    'title' => 'View Event Details',
                    'route' => '/api/v1/events/{id}',
                    'method' => 'GET',
                    'description' => 'Detailed information about a specific event.',
                ],
            ],
            'News' => [
                [
                    'title' => 'List News',
                    'route' => '/api/v1/news',
                    'method' => 'GET',
                    'description' => 'Paginated list of latest news and updates.',
                ],
            ],
            'Documents' => [
                [
                    'title' => 'List Documents',
                    'route' => '/api/v1/documents',
                    'method' => 'GET',
                    'description' => 'Browse public documents and session files.',
                ],
            ],
            'Authentication' => [
                [
                    'title' => 'Token Handoff',
                    'route' => '/api/v1/auth/token-handoff',
                    'method' => 'GET',
                    'description' => 'Convert a Web session to a permanent Sanctum API token (Hybrid Login).',
                ],
                [
                    'title' => 'My Profile',
                    'route' => '/api/v1/auth/me',
                    'method' => 'GET',
                    'description' => 'Fetch the currently authenticated user details.',
                ],
            ],
        ];

        $predefinedCodes = config('api-docs-builder.importer.predefined_codes', ['cURL', 'Laravel', 'PHP']);

        foreach ($categories as $catName => $items) {
            $data = [];
            foreach ($items as $item) {
                $data[] = [
                    'details' => [
                        'title' => $item['title'],
                        'description' => $item['description'],
                        'endpoint' => $item['route'],
                        'method' => $item['method'],
                        'request_type' => $item['method'],
                        'auth_required' => str_contains($item['route'], '/auth/'),
                        'collapsed' => false,
                    ],
                    'instructions' => [
                        'params' => [],
                    ],
                    'request_code' => [
                        'use_predefined_codes' => $predefinedCodes,
                        'use_custom_codes' => false,
                        'custom_code' => [],
                    ],
                    'token' => '', // Initialize token to prevent Undefined Key error
                    'header' => [],
                    'query' => [],
                    'body' => [],
                    'route' => [],
                    'response' => [
                        [
                            'title' => 'Success Response',
                            'description' => 'Returns the requested data.',
                            'body' => json_encode(['status' => 'success', 'data' => []], JSON_PRETTY_PRINT),
                            'icon' => 'heroicon-o-check-circle',
                            'color' => 'green',
                        ],
                    ],
                ];
            }

            ApiDocs::updateOrCreate(
                ['slug' => Str::slug($catName)],
                [
                    'title' => $catName.' API',
                    'description' => "API endpoints for $catName",
                    'version' => '1.0.0',
                    'data' => $data,
                ]
            );
        }
    }
}
