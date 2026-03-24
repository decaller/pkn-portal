<?php

namespace Database\Seeders;

use App\Enums\PaymentStatus;
use App\Enums\RegistrationStatus;
use App\Models\Document;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\News;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ApiResultSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create Users
        $admin = User::updateOrCreate(
            ['email' => 'harridiilmantovid@gmail.com'],
            [
                'name' => 'Admin',
                'phone_number' => '081100000001',
                'password' => Hash::make('cemara153'),
                'is_super_admin' => true,
            ],
        );

        $ybitAdmin = User::updateOrCreate(
            ['email' => 'admin.ybit@example.com'],
            [
                'name' => 'YBIT Admin',
                'phone_number' => '081100000002',
                'password' => Hash::make('password123'),
                'is_super_admin' => false,
            ],
        );

        $ybitMember = User::updateOrCreate(
            ['email' => 'member.ybit@example.com'],
            [
                'name' => 'YBIT Member',
                'phone_number' => '081100000003',
                'password' => Hash::make('password123'),
                'is_super_admin' => false,
            ],
        );

        // 2. Create Organizations
        $pkn = Organization::updateOrCreate(
            ['slug' => 'pkn'],
            [
                'name' => 'PKN',
                'admin_user_id' => $admin->id,
            ],
        );

        $ybit = Organization::updateOrCreate(
            ['slug' => 'ybit'],
            [
                'name' => 'Yayasan Bina Insan Taqwa',
                'admin_user_id' => $ybitAdmin->id,
            ],
        );

        $ybit->users()->syncWithoutDetaching([
            $ybitAdmin->id => ['role' => 'admin'],
            $ybitMember->id => ['role' => 'member'],
        ]);

        // 3. Create Events
        $this->seedEvents();

        // 4. Create News
        $this->seedNews();

        // 5. Create Documents
        $this->seedDocuments();

        // 6. Create Registrations
        $this->seedRegistrations($ybit, $ybitMember);
    }

    private function seedEvents(): void
    {
        $coverFiles = \File::files(base_path('seeder content/event cover image'));
        $proposalFiles = \File::files(base_path('seeder content/event proposal'));
        $documentationFiles = \File::files(base_path('seeder content/event photos documentation'));
        $sessionFiles = \File::files(base_path('seeder content/documents'));

        $events = [
            [
                'id' => 1,
                'title' => 'PKN National Conference 2026',
                'slug' => 'pkn-national-conference-2026',
                'description' => '<p>Annual national offline conference by PKN.</p>',
                'event_date' => '2026-06-04',
                'is_published' => true,
                'allow_registration' => true,
                'registration_packages' => [
                    ['name' => 'Regular', 'price' => 100000],
                    ['name' => 'VIP', 'price' => 250000],
                ],
                'cover_image' => $this->copyToPublic($coverFiles[1] ?? null, 'event-covers'),
                'proposal' => $this->copyToPublic($proposalFiles[1] ?? null, 'event-proposals'),
                'documentation' => array_filter([
                    $this->copyToPublic($documentationFiles[1] ?? null, 'event-documentation'),
                    $this->copyToPublic($documentationFiles[2] ?? null, 'event-documentation'),
                ]),
                'rundown' => [
                    [
                        'type' => 'advanced',
                        'data' => [
                            'title' => 'Ju',
                            'session_files' => array_filter([
                                $this->copyToPublic($sessionFiles[0] ?? null, 'events/pkn-national-conference-2026/sessions'),
                                $this->copyToPublic($sessionFiles[1] ?? null, 'events/pkn-national-conference-2026/sessions'),
                            ]),
                        ],
                    ],
                ],
            ],
            [
                'id' => 2,
                'title' => 'PKN Regional Workshop 2026',
                'slug' => 'pkn-regional-workshop-2026',
                'description' => '<p>Workshop discussion.</p>',
                'event_date' => '2026-09-04',
                'city' => 'Kabupaten Cikarang',
                'province' => 'Jawa Barat',
                'nation' => 'Indonesia',
                'duration_days' => 5,
                'is_published' => true,
                'allow_registration' => true,
                'max_capacity' => 50,
                'registration_packages' => [
                    ['name' => 'Regular', 'price' => 75000, 'max_quota' => 30],
                    ['name' => 'VIP', 'price' => 200000, 'max_quota' => 20],
                ],
                'cover_image' => $this->copyToPublic($coverFiles[0] ?? null, 'event-covers'),
                'proposal' => $this->copyToPublic($proposalFiles[0] ?? null, 'event-proposals'),
                'documentation' => array_filter([
                    $this->copyToPublic($documentationFiles[3] ?? null, 'event-documentation'),
                    $this->copyToPublic($documentationFiles[4] ?? null, 'event-documentation'),
                    $this->copyToPublic($documentationFiles[5] ?? null, 'event-documentation'),
                ]),
                'rundown' => [
                    [
                        'type' => 'advanced',
                        'data' => [
                            'title' => 'Materi Sesi 1',
                            'speaker' => 'Ustad Abdul Kholiq',
                            'session_files' => array_filter([
                                $this->copyToPublic($sessionFiles[2] ?? null, 'events/pkn-regional-workshop-2026/sessions'),
                                $this->copyToPublic($sessionFiles[3] ?? null, 'events/pkn-regional-workshop-2026/sessions'),
                            ]),
                        ],
                    ],
                ],
            ],
            [
                'id' => 3,
                'title' => 'PKN Recap 2023',
                'slug' => 'pkn-recap-2023',
                'event_date' => '2025-03-04',
                'is_published' => true,
                'allow_registration' => false,
            ],
            [
                'id' => 4,
                'title' => 'PKN Mini Summit 2024',
                'slug' => 'pkn-mini-summit-2024',
                'event_date' => '2025-07-04',
                'is_published' => true,
                'allow_registration' => false,
                'city' => 'Bandung',
                'province' => 'Jawa Barat',
                'nation' => 'Indonesia',
            ],
            [
                'id' => 5,
                'title' => 'Sed quas officiis suscipit.',
                'slug' => 'numquam-sit-doloribus-est-natus',
                'event_date' => '2026-04-22',
                'is_published' => true,
                'allow_registration' => true,
            ],
        ];

        foreach ($events as $eventData) {
            Event::updateOrCreate(['slug' => $eventData['slug']], $eventData);
        }
    }

    private function seedNews(): void
    {
        $coverFiles = \File::files(base_path('seeder content/event cover image'));

        $news = [
            [
                'id' => 1,
                'title' => 'Registration Open: PKN National Conference 2026',
                'content' => '<p>Registration is now open for PKN National Conference 2026.</p>',
                'thumbnail' => $this->copyToPublic($coverFiles[2] ?? null, 'news-thumbnails'),
                'is_published' => true,
            ],
            [
                'id' => 2,
                'title' => 'Registration Open: PKN Regional Workshop 2026',
                'content' => '<p>Registration is now open for PKN Regional Workshop 2026.</p>',
                'thumbnail' => $this->copyToPublic($coverFiles[3] ?? null, 'news-thumbnails'),
                'is_published' => true,
            ],
        ];

        foreach ($news as $newsData) {
            News::updateOrCreate(['title' => $newsData['title']], $newsData);
        }
    }

    private function seedDocuments(): void
    {
        $sessionFiles = \File::files(base_path('seeder content/documents'));
        $coverFiles = \File::files(base_path('seeder content/event cover image'));

        $docs = [
            [
                'id' => 27,
                'title' => '1. Mengembalikan Pendidikan ke asalnya-1.pdf',
                'slug' => 'doc-a7z0y',
                'file_path' => $this->copyToPublic($sessionFiles[4] ?? null, 'manual-uploads'),
                'original_filename' => $sessionFiles[4] ? $sessionFiles[4]->getFilename() : '1. Mengembalikan Pendidikan ke asalnya-1.pdf',
                'cover_image' => $this->copyToPublic($coverFiles[4] ?? null, 'document-covers'),
                'mime_type' => 'application/pdf',
                'tags' => ['featured'],
                'is_active' => true,
            ],
            [
                'id' => 34,
                'title' => '0. PROGRAM PEMBELAJARAN 40 PILAR.xlsx',
                'slug' => 'doc-pnxci',
                'file_path' => $this->copyToPublic($sessionFiles[5] ?? null, 'manual-uploads'),
                'original_filename' => $sessionFiles[5] ? $sessionFiles[5]->getFilename() : '0. PROGRAM PEMBELAJARAN 40 PILAR.xlsx',
                'tags' => ['featured'],
                'is_active' => true,
            ],
            [
                'id' => 30,
                'title' => '4. BAKAT - TB - 40.pptx',
                'slug' => 'doc-pvpme',
                'file_path' => $this->copyToPublic($sessionFiles[0] ?? null, 'manual-uploads'),
                'original_filename' => $sessionFiles[0] ? $sessionFiles[0]->getFilename() : '4. BAKAT - TB - 40.pptx',
                'tags' => ['featured'],
                'is_active' => true,
            ],
            [
                'id' => 31,
                'title' => '3. Pembelajaran Alamiyah.pptx',
                'slug' => 'doc-tqtyz',
                'file_path' => $this->copyToPublic($sessionFiles[1] ?? null, 'manual-uploads'),
                'original_filename' => $sessionFiles[1] ? $sessionFiles[1]->getFilename() : '3. Pembelajaran Alamiyah.pptx',
                'tags' => ['featured'],
                'is_active' => true,
            ],
        ];

        foreach ($docs as $docData) {
            Document::updateOrCreate(['slug' => $docData['slug']], $docData);
        }
    }

    private function seedRegistrations(Organization $ybit, User $member): void
    {
        $event = Event::where('slug', 'pkn-national-conference-2026')->first();

        if ($event) {
            EventRegistration::updateOrCreate(
                [
                    'event_id' => $event->id,
                    'booker_user_id' => $member->id,
                ],
                [
                    'organization_id' => $ybit->id,
                    'status' => RegistrationStatus::Draft,
                    'payment_status' => PaymentStatus::Unpaid,
                    'total_amount' => 100000.00,
                    'package_breakdown' => [
                        [
                            'package_name' => 'Regular',
                            'participant_count' => 1,
                            'unit_price' => 100000,
                        ],
                    ],
                ]
            );
        }
    }

    private function copyToPublic(?\Symfony\Component\Finder\SplFileInfo $sourceFile, string $destinationDir): ?string
    {
        if (! $sourceFile) {
            return null;
        }

        $filename = $sourceFile->getFilename();
        $destinationPath = $destinationDir . '/' . $filename;

        if (! Storage::disk('public')->exists($destinationPath)) {
            Storage::disk('public')->put($destinationPath, file_get_contents($sourceFile->getRealPath()));
        }

        return $destinationPath;
    }
}
