<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'PKN Portal') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@5" rel="stylesheet" type="text/css" />
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@5/themes.css" rel="stylesheet" type="text/css" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Space Grotesk', ui-sans-serif, system-ui, sans-serif;
        }

        .outline-title {
            color: transparent;
            -webkit-text-stroke: 1px color-mix(in oklab, currentColor 55%, transparent);
            letter-spacing: 0.01em;
        }
    </style>
    <script>
        (function() {
            var root = document.documentElement;
            var media = window.matchMedia('(prefers-color-scheme: dark)');
            var storageKey = 'pkn-theme';

            var setTheme = function(theme) {
                root.setAttribute('data-theme', theme);
            };

            var getPreferredTheme = function() {
                var saved = localStorage.getItem(storageKey);
                if (saved === 'forest' || saved === 'garden') {
                    return saved;
                }

                return media.matches ? 'forest' : 'garden';
            };

            var updateToggleLabel = function() {
                var button = document.getElementById('theme-toggle');
                if (!button) {
                    return;
                }

                var current = root.getAttribute('data-theme');
                var isId = root.getAttribute('lang') === 'id';
                if (current === 'forest') {
                    button.textContent = isId ? 'Ubah ke Mode Terang' : 'Switch to Light Mode';
                } else {
                    button.textContent = isId ? 'Ubah ke Mode Gelap' : 'Switch to Dark Mode';
                }
            };

            setTheme(getPreferredTheme());

            media.addEventListener('change', function(event) {
                if (localStorage.getItem(storageKey)) {
                    return;
                }

                setTheme(event.matches ? 'forest' : 'garden');
                updateToggleLabel();
            });

            document.addEventListener('DOMContentLoaded', function() {
                var button = document.getElementById('theme-toggle');
                updateToggleLabel();

                if (!button) {
                    return;
                }

                button.addEventListener('click', function() {
                    var nextTheme = root.getAttribute('data-theme') === 'forest' ? 'garden' : 'forest';
                    setTheme(nextTheme);
                    localStorage.setItem(storageKey, nextTheme);
                    updateToggleLabel();
                });
            });
        })();
    </script>
</head>

@php
    use App\Models\Event;
    use App\Models\News;
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Str;

    $tr = fn (string $en, string $id): string => app()->getLocale() === 'id' ? $id : $en;

    $upcomingEvents = Event::query()
        ->where('is_published', true)
        ->whereDate('event_date', '>=', now()->toDateString())
        ->orderBy('event_date')
        ->take(6)
        ->get();

    $pastEvents = Event::query()
        ->where('is_published', true)
        ->whereDate('event_date', '<', now()->toDateString())
        ->orderByDesc('event_date')
        ->take(6)
        ->get();

    $latestNews = News::query()
        ->where('is_published', true)
        ->latest()
        ->take(3)
        ->get();

    $eventImageUrl = function (?string $path): ?string {
        if (! $path) {
            return null;
        }

        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        try {
            return Storage::url($path);
        } catch (\Throwable) {
            return null;
        }
    };

    $eventLocation = function (Event $event): string {
        return collect([$event->place, $event->city, $event->province, $event->nation])->filter()->implode(', ');
    };

    $daysUntilEvent = function (Event $event): int {
        if (! $event->event_date) {
            return 0;
        }

        return max(0, now()->startOfDay()->diffInDays($event->event_date, false));
    };

    $daysSinceEvent = function (Event $event): int {
        if (! $event->event_date) {
            return 0;
        }

        return abs(min(0, now()->startOfDay()->diffInDays($event->event_date, false)));
    };
@endphp

<body class="min-h-screen bg-base-200 text-base-content">
    <div class="mx-auto max-w-7xl px-4 py-6 md:py-10">
        <header class="mb-10 border-b border-base-300 pb-5">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <a href="{{ route('home') }}" class="text-2xl font-bold leading-none">PKN <span class="font-medium">PORTAL</span></a>
                <nav class="flex flex-wrap items-center gap-5 text-sm uppercase tracking-wide text-base-content/70">
                    <a href="{{ route('filament.public.resources.events.index') }}" class="hover:text-base-content">{{ $tr('Events', 'Acara') }}</a>
                    <a href="{{ route('filament.public.resources.news.index') }}" class="hover:text-base-content">{{ $tr('News', 'Berita') }}</a>
                    <a href="{{ route('filament.user.auth.login') }}" class="hover:text-base-content">{{ $tr('User Login', 'Masuk User') }}</a>
                    <a href="{{ route('filament.admin.auth.login') }}" class="hover:text-base-content">Admin</a>
                </nav>
                <div class="join">
                    <a href="{{ route('locale.switch', ['locale' => 'id']) }}" class="join-item btn btn-xs {{ app()->getLocale() === 'id' ? 'btn-primary' : 'btn-ghost' }}">ID</a>
                    <a href="{{ route('locale.switch', ['locale' => 'en']) }}" class="join-item btn btn-xs {{ app()->getLocale() === 'en' ? 'btn-primary' : 'btn-ghost' }}">EN</a>
                </div>
            </div>
        </header>

        <main>
            <section class="mb-12 border-b border-base-300 pb-8">
                <nav class="mb-5 flex items-center gap-2 text-xs uppercase tracking-wide text-base-content/50">
                    <span>{{ $tr('Home', 'Beranda') }}</span>
                    <span>/</span>
                    <span class="text-base-content/80">{{ $tr('Events', 'Acara') }}</span>
                </nav>

                <div class="grid gap-6 md:grid-cols-[1fr_auto] md:items-end">
                    <div>
                        <h1 class="text-5xl font-bold leading-none md:text-7xl">{{ $tr('Events', 'Acara') }}</h1>
                        <p class="mt-5 max-w-2xl text-sm text-base-content/70 md:text-base">
                            {{ $tr('University events take place throughout the year, from educational showcases to public lectures, national tours and one-off exhibitions.', 'Acara berlangsung sepanjang tahun, dari showcase pendidikan hingga kuliah umum, tur nasional, dan pameran khusus.') }}
                        </p>
                    </div>
                    <div class="hidden md:block">
                        <span class="outline-title text-7xl font-bold uppercase opacity-30 md:text-9xl">{{ $tr('Events', 'Acara') }}</span>
                    </div>
                </div>
            </section>

            <section class="mb-12">
                <div class="mb-5 flex items-center justify-between border-b border-base-300 pb-2">
                    <h2 class="text-2xl font-bold uppercase">{{ $tr('Upcoming', 'Mendatang') }}</h2>
                    <a href="{{ route('filament.public.resources.events.index') }}" class="text-xs uppercase tracking-wider text-base-content/70 hover:text-base-content">{{ $tr('See all events', 'Lihat semua acara') }}</a>
                </div>

                @if ($upcomingEvents->isNotEmpty())
                    <div class="grid gap-6 md:grid-cols-2">
                        @foreach ($upcomingEvents as $event)
                            <article class="border border-base-300 bg-base-100">
                                <div class="p-4">
                                    <p class="mb-1 text-[11px] uppercase tracking-[0.18em] text-base-content/60">{{ $eventLocation($event) ?: $tr('Location TBA', 'Lokasi menyusul') }}</p>
                                    <h4 class="line-clamp-2 text-2xl font-bold leading-tight">{{ $event->title }}</h4>
                                    <div class="mt-3 flex items-center justify-between text-xs uppercase tracking-wide text-base-content/70">
                                        <span>{{ $event->event_date?->format('d M Y') }}</span>
                                        <span>{{ $daysUntilEvent($event) }} {{ $tr('days left', 'hari lagi') }}</span>
                                    </div>
                                </div>
                                <figure class="h-56 w-full bg-base-300">
                                    @if ($eventImageUrl($event->cover_image))
                                        <img src="{{ $eventImageUrl($event->cover_image) }}" alt="{{ $event->title }}" class="h-full w-full object-cover" />
                                    @else
                                        <div class="flex h-full w-full items-center justify-center text-base-content/50">{{ $tr('No image', 'Tidak ada gambar') }}</div>
                                    @endif
                                </figure>
                                <div class="flex border-t border-base-300 text-[10px] uppercase tracking-wider">
                                    @if ($event->allow_registration)
                                        <a href="{{ route('filament.user.auth.register', ['event_id' => $event->getKey()]) }}" class="flex-1 px-3 py-2 text-center hover:bg-base-200">{{ $tr('Register', 'Daftar') }}</a>
                                        <a href="{{ route('filament.public.resources.events.view', ['record' => $event]) }}" class="flex-1 border-l border-base-300 px-3 py-2 text-center hover:bg-base-200">{{ $tr('More info', 'Detail') }}</a>
                                    @else
                                        <a href="{{ route('filament.public.resources.events.view', ['record' => $event]) }}" class="w-full px-3 py-2 text-center hover:bg-base-200">{{ $tr('More info', 'Detail') }}</a>
                                    @endif
                                </div>
                            </article>
                        @endforeach
                    </div>
                @else
                    <div class="alert"><span>{{ $tr('No upcoming events published yet.', 'Belum ada acara mendatang yang dipublikasikan.') }}</span></div>
                @endif
            </section>

            <section class="mb-12">
                <div class="mb-5 flex items-center justify-between border-b border-base-300 pb-2">
                    <h2 class="text-2xl font-bold uppercase">{{ $tr('Latest News', 'Berita Terbaru') }}</h2>
                    <a href="{{ route('filament.public.resources.news.index') }}" class="text-xs uppercase tracking-wider text-base-content/70 hover:text-base-content">{{ $tr('View all', 'Lihat semua') }}</a>
                </div>
                <div class="grid gap-4 md:grid-cols-3">
                    @forelse ($latestNews as $news)
                        <article class="border border-base-300 bg-base-100 p-4">
                            <p class="text-[11px] uppercase tracking-[0.18em] text-base-content/60">{{ $news->created_at?->format('d M Y') }}</p>
                            <h3 class="mt-2 line-clamp-2 text-xl font-bold leading-tight">{{ $news->title }}</h3>
                            <p class="mt-3 text-sm text-base-content/75">{{ Str::limit(strip_tags($news->content), 130) }}</p>
                            <div class="mt-4 border-t border-base-300 pt-3 text-right">
                                <a href="{{ route('filament.public.resources.news.view', ['record' => $news]) }}" class="text-xs uppercase tracking-wider hover:underline">{{ $tr('Read more', 'Baca selengkapnya') }}</a>
                            </div>
                        </article>
                    @empty
                        <div class="alert md:col-span-3"><span>{{ $tr('No published news yet.', 'Belum ada berita yang dipublikasikan.') }}</span></div>
                    @endforelse
                </div>
            </section>

            <section class="mb-12">
                <div class="mb-5 flex items-center justify-between border-b border-base-300 pb-2">
                    <h2 class="text-2xl font-bold uppercase">{{ $tr('Past Events', 'Acara Lampau') }}</h2>
                    <a href="{{ route('filament.public.resources.events.index') }}" class="text-xs uppercase tracking-wider text-base-content/70 hover:text-base-content">{{ $tr('Browse archive', 'Lihat arsip') }}</a>
                </div>
                <div class="grid gap-4 md:grid-cols-3">
                    @forelse ($pastEvents as $event)
                        <article class="border border-base-300 bg-base-100">
                            <div class="p-4">
                                <p class="text-[11px] uppercase tracking-[0.18em] text-base-content/60">{{ $eventLocation($event) ?: $tr('Location TBA', 'Lokasi menyusul') }}</p>
                                <h3 class="mt-2 line-clamp-2 text-lg font-bold leading-tight">{{ $event->title }}</h3>
                                <div class="mt-3 flex items-center justify-between text-xs uppercase tracking-wide text-base-content/70">
                                    <span>{{ $event->event_date?->format('d M Y') }}</span>
                                    <span>{{ $daysSinceEvent($event) }} {{ $tr('days ago', 'hari lalu') }}</span>
                                </div>
                            </div>
                            <figure class="h-40 w-full bg-base-300">
                                @if ($eventImageUrl($event->cover_image))
                                    <img src="{{ $eventImageUrl($event->cover_image) }}" alt="{{ $event->title }}" class="h-full w-full object-cover grayscale" />
                                @else
                                    <div class="flex h-full w-full items-center justify-center text-base-content/50">{{ $tr('No image', 'Tidak ada gambar') }}</div>
                                @endif
                            </figure>
                            <a href="{{ route('filament.public.resources.events.view', ['record' => $event]) }}" class="block border-t border-base-300 px-3 py-2 text-center text-[10px] uppercase tracking-wider hover:bg-base-200">{{ $tr('View recap', 'Lihat ringkasan') }}</a>
                        </article>
                    @empty
                        <div class="alert md:col-span-3"><span>{{ $tr('No past events yet.', 'Belum ada acara lampau.') }}</span></div>
                    @endforelse
                </div>
            </section>

            <section class="mb-12 border border-base-300 bg-base-100 p-6 md:p-8">
                <div class="grid gap-6 md:grid-cols-2 md:items-center">
                    <div>
                        <p class="text-xs uppercase tracking-[0.2em] text-base-content/60">{{ $tr('Join the portal', 'Gabung portal') }}</p>
                        <h3 class="mt-2 text-3xl font-bold md:text-4xl">{{ $tr('Manage participants, files, and event access faster.', 'Kelola peserta, berkas, dan akses acara lebih cepat.') }}</h3>
                        <p class="mt-3 text-sm text-base-content/70">{{ $tr('Members get access to digital files from previous events and easier participant management in one dashboard.', 'Member mendapatkan akses file digital acara sebelumnya dan manajemen peserta yang lebih mudah dalam satu dashboard.') }}</p>
                    </div>
                    <div class="grid gap-2 sm:grid-cols-2">
                        <a href="{{ route('filament.user.auth.register') }}" class="btn btn-primary sm:col-span-2">{{ $tr('Sign Up', 'Daftar') }}</a>
                        <a href="{{ route('filament.user.auth.login') }}" class="btn btn-outline">{{ $tr('User Login', 'Masuk User') }}</a>
                        <a href="{{ route('filament.admin.auth.login') }}" class="btn btn-outline">Admin Login</a>
                    </div>
                </div>
            </section>
        </main>

        <footer class="border-t border-base-300 pt-8">
            <div class="grid gap-6 md:grid-cols-3">
                <div>
                    <p class="text-xl font-bold">PKN Portal</p>
                    <p class="mt-2 text-sm text-base-content/70">{{ $tr('Public events, user registrations, and admin operations in one platform.', 'Acara publik, registrasi user, dan operasional admin dalam satu platform.') }}</p>
                </div>
                <div>
                    <p class="text-sm font-semibold uppercase tracking-wide">{{ $tr('Quick Links', 'Tautan Cepat') }}</p>
                    <div class="mt-3 flex flex-col gap-2 text-sm text-base-content/80">
                        <a href="{{ route('filament.public.pages.dashboard') }}" class="hover:underline">{{ $tr('Public Panel', 'Panel Publik') }}</a>
                        <a href="{{ route('filament.user.auth.login') }}" class="hover:underline">{{ $tr('User Login', 'Masuk User') }}</a>
                        <a href="{{ route('filament.user.auth.register') }}" class="hover:underline">{{ $tr('User Register', 'Daftar User') }}</a>
                        <a href="{{ route('filament.admin.auth.login') }}" class="hover:underline">Admin Login</a>
                    </div>
                </div>
                <div class="flex items-start md:justify-end">
                    <button id="theme-toggle" type="button" class="btn btn-outline btn-sm">Switch to Dark Mode</button>
                </div>
            </div>
        </footer>
    </div>
</body>

</html>
