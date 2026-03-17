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
                if (current === 'forest') {
                    button.textContent = "{{ __('Switch to Light Mode') }}";
                } else {
                    button.textContent = "{{ __('Switch to Dark Mode') }}";
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
    use App\Models\Setting;
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Str;



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

    $contactNumber = Setting::defaultContactNumber();
    $contactWhatsAppUrl = Setting::defaultContactWhatsAppUrl('Hello, I need support for PKN Portal.');
@endphp

<body class="min-h-screen bg-base-200 text-base-content">
    <div class="mx-auto max-w-7xl px-4 py-6 md:py-10">
        <header class="mb-10 border-b border-base-300 pb-5">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <a href="{{ route('home') }}" class="text-2xl font-bold leading-none">PKN <span class="font-medium">PORTAL</span></a>
                <nav class="flex flex-wrap items-center gap-5 text-sm uppercase tracking-wide text-base-content/70">
                    <a href="{{ route('filament.public.resources.events.index') }}" class="hover:text-base-content">{{ __('Events') }}</a>
                    <a href="{{ route('filament.public.resources.news.index') }}" class="hover:text-base-content">{{ __('News') }}</a>
                    <a href="{{ route('filament.user.auth.login') }}" class="hover:text-base-content">{{ __('User Login') }}</a>
                    <a href="{{ route('filament.admin.auth.login') }}" class="hover:text-base-content">{{ __('Admin Login') }}</a>
                </nav>
                <div class="flex items-center gap-2">
                    @if ($contactNumber && $contactWhatsAppUrl)
                        <a href="{{ $contactWhatsAppUrl }}" target="_blank" rel="noopener noreferrer" class="btn btn-outline btn-xs bg-white text-base-content hover:bg-base-100">
                            {{ __('Help') }}
                        </a>
                    @endif
                    <div class="join">
                        <a href="{{ route('locale.switch', ['locale' => 'id']) }}" class="join-item btn btn-xs {{ app()->getLocale() === 'id' ? 'btn-primary' : 'btn-ghost' }}">ID</a>
                        <a href="{{ route('locale.switch', ['locale' => 'en']) }}" class="join-item btn btn-xs {{ app()->getLocale() === 'en' ? 'btn-primary' : 'btn-ghost' }}">EN</a>
                    </div>
                </div>
            </div>
        </header>

        <main>
            <section class="mb-12 border-b border-base-300 pb-8">
                <nav class="mb-5 flex items-center gap-2 text-xs uppercase tracking-wide text-base-content/50">
                    <span>{{ __('Home') }}</span>
                    <span>/</span>
                    <span class="text-base-content/80">{{ __('Events') }}</span>
                </nav>

                <div class="grid gap-6 md:grid-cols-[1fr_auto] md:items-end">
                    <div>
                        <h1 class="text-5xl font-bold leading-none md:text-7xl">{{ __('Events') }}</h1>
                        <p class="mt-5 max-w-2xl text-sm text-base-content/70 md:text-base">
                            {{ __('University events take place throughout the year, from educational showcases to public lectures, national tours and one-off exhibitions.') }}
                        </p>
                    </div>
                    <div class="hidden md:block">
                        <span class="outline-title text-7xl font-bold uppercase opacity-30 md:text-9xl">{{ __('Events') }}</span>
                    </div>
                </div>
            </section>

            <section class="mb-12">
                <div class="mb-5 flex items-center justify-between border-b border-base-300 pb-2">
                    <h2 class="text-2xl font-bold uppercase">{{ __('Upcoming') }}</h2>
                    <a href="{{ route('filament.public.resources.events.index') }}" class="text-xs uppercase tracking-wider text-base-content/70 hover:text-base-content">{{ __('See all events') }}</a>
                </div>

                @if ($upcomingEvents->isNotEmpty())
                    <div class="grid gap-6 md:grid-cols-2">
                        @foreach ($upcomingEvents as $event)
                            <article class="border border-base-300 bg-base-100">
                                <div class="p-4">
                                    <p class="mb-1 text-[11px] uppercase tracking-[0.18em] text-base-content/60">{{ $eventLocation($event) ?: __('Location TBA') }}</p>
                                    <h4 class="line-clamp-2 text-2xl font-bold leading-tight">{{ $event->title }}</h4>
                                    <div class="mt-3 flex items-center justify-between text-xs uppercase tracking-wide text-base-content/70">
                                        <span>{{ $event->event_date?->format('d M Y') }}</span>
                                        <span>{{ $daysUntilEvent($event) }} {{ __('days left') }}</span>
                                    </div>
                                </div>
                                <figure class="h-56 w-full bg-base-300">
                                    @if ($eventImageUrl($event->cover_image))
                                        <img src="{{ $eventImageUrl($event->cover_image) }}" alt="{{ $event->title }}" class="h-full w-full object-cover" />
                                    @else
                                        <div class="flex h-full w-full items-center justify-center text-base-content/50">{{ __('No image') }}</div>
                                    @endif
                                </figure>
                                <div class="flex border-t border-base-300 text-[10px] uppercase tracking-wider">
                                    @if ($event->allow_registration)
                                        <a href="{{ route('filament.user.auth.register', ['event_id' => $event->getKey()]) }}" class="flex-1 px-3 py-2 text-center hover:bg-base-200">{{ __('Register') }}</a>
                                        <a href="{{ route('filament.public.resources.events.view', ['record' => $event]) }}" class="flex-1 border-l border-base-300 px-3 py-2 text-center hover:bg-base-200">{{ __('More info') }}</a>
                                    @else
                                        <a href="{{ route('filament.public.resources.events.view', ['record' => $event]) }}" class="w-full px-3 py-2 text-center hover:bg-base-200">{{ __('More info') }}</a>
                                    @endif
                                </div>
                            </article>
                        @endforeach
                    </div>
                @else
                    <div class="alert"><span>{{ __('No upcoming events published yet.') }}</span></div>
                @endif
            </section>

            <section class="mb-12">
                <div class="mb-5 flex items-center justify-between border-b border-base-300 pb-2">
                    <h2 class="text-2xl font-bold uppercase">{{ __('Latest News') }}</h2>
                    <a href="{{ route('filament.public.resources.news.index') }}" class="text-xs uppercase tracking-wider text-base-content/70 hover:text-base-content">{{ __('View all') }}</a>
                </div>
                <div class="grid gap-4 md:grid-cols-3">
                    @forelse ($latestNews as $news)
                        <article class="border border-base-300 bg-base-100 p-4">
                            <p class="text-[11px] uppercase tracking-[0.18em] text-base-content/60">{{ $news->created_at?->format('d M Y') }}</p>
                            <h3 class="mt-2 line-clamp-2 text-xl font-bold leading-tight">{{ $news->title }}</h3>
                            <p class="mt-3 text-sm text-base-content/75">{{ Str::limit(strip_tags($news->content), 130) }}</p>
                            <div class="mt-4 border-t border-base-300 pt-3 text-right">
                                <a href="{{ route('filament.public.resources.news.view', ['record' => $news]) }}" class="text-xs uppercase tracking-wider hover:underline">{{ __('Read more') }}</a>
                            </div>
                        </article>
                    @empty
                        <div class="alert md:col-span-3"><span>{{ __('No published news yet.') }}</span></div>
                    @endforelse
                </div>
            </section>

            <section class="mb-12">
                <div class="mb-5 flex items-center justify-between border-b border-base-300 pb-2">
                    <h2 class="text-2xl font-bold uppercase">{{ __('Past Events') }}</h2>
                    <a href="{{ route('filament.public.resources.events.index') }}" class="text-xs uppercase tracking-wider text-base-content/70 hover:text-base-content">{{ __('Browse archive') }}</a>
                </div>
                <div class="grid gap-4 md:grid-cols-3">
                    @forelse ($pastEvents as $event)
                        <article class="border border-base-300 bg-base-100">
                            <div class="p-4">
                                <p class="text-[11px] uppercase tracking-[0.18em] text-base-content/60">{{ $eventLocation($event) ?: __('Location TBA') }}</p>
                                <h3 class="mt-2 line-clamp-2 text-lg font-bold leading-tight">{{ $event->title }}</h3>
                                <div class="mt-3 flex items-center justify-between text-xs uppercase tracking-wide text-base-content/70">
                                    <span>{{ $event->event_date?->format('d M Y') }}</span>
                                    <span>{{ $daysSinceEvent($event) }} {{ __('days ago') }}</span>
                                </div>
                            </div>
                            <figure class="h-40 w-full bg-base-300">
                                @if ($eventImageUrl($event->cover_image))
                                    <img src="{{ $eventImageUrl($event->cover_image) }}" alt="{{ $event->title }}" class="h-full w-full object-cover grayscale" />
                                @else
                                    <div class="flex h-full w-full items-center justify-center text-base-content/50">{{ __('No image') }}</div>
                                @endif
                            </figure>
                            <a href="{{ route('filament.public.resources.events.view', ['record' => $event]) }}" class="block border-t border-base-300 px-3 py-2 text-center text-[10px] uppercase tracking-wider hover:bg-base-200">{{ __('View recap') }}</a>
                        </article>
                    @empty
                        <div class="alert md:col-span-3"><span>{{ __('No past events yet.') }}</span></div>
                    @endforelse
                </div>
            </section>

            <section class="mb-12 border border-base-300 bg-base-100 p-6 md:p-8">
                <div class="grid gap-6 md:grid-cols-2 md:items-center">
                    <div>
                        <p class="text-xs uppercase tracking-[0.2em] text-base-content/60">{{ __('Join the portal') }}</p>
                        <h3 class="mt-2 text-3xl font-bold md:text-4xl">{{ __('Manage participants, files, and event access faster.') }}</h3>
                        <p class="mt-3 text-sm text-base-content/70">{{ __('Members get access to digital files from previous events and easier participant management in one dashboard.') }}</p>
                    </div>
                    <div class="grid gap-2 sm:grid-cols-2">
                        <a href="{{ route('filament.user.auth.register') }}" class="btn btn-primary sm:col-span-2">{{ __('Sign Up') }}</a>
                        <a href="{{ route('filament.user.auth.login') }}" class="btn btn-outline">{{ __('User Login') }}</a>
                        <a href="{{ route('filament.admin.auth.login') }}" class="btn btn-outline">{{ __('Admin Login') }}</a>
                    </div>
                </div>
            </section>
        </main>

        <footer class="border-t border-base-300 pt-8">
            <div class="grid gap-6 md:grid-cols-3">
                <div>
                    <p class="text-xl font-bold">PKN Portal</p>
                    <p class="mt-2 text-sm text-base-content/70">{{ __('Public events, user registrations, and admin operations in one platform.') }}</p>
                </div>
                <div>
                    <p class="text-sm font-semibold uppercase tracking-wide">{{ __('Quick Links') }}</p>
                    <div class="mt-3 flex flex-col gap-2 text-sm text-base-content/80">
                        <a href="{{ route('filament.public.pages.dashboard') }}" class="hover:underline">{{ __('Public Panel') }}</a>
                        <a href="{{ route('filament.user.auth.login') }}" class="hover:underline">{{ __('User Login') }}</a>
                        <a href="{{ route('filament.user.auth.register') }}" class="hover:underline">{{ __('User Register') }}</a>
                        <a href="{{ route('filament.admin.auth.login') }}" class="hover:underline">{{ __('Admin Login') }}</a>
                    </div>
                </div>
                <div class="flex items-start md:justify-end">
                    <div class="flex flex-col items-stretch gap-2">
                        @if ($contactNumber && $contactWhatsAppUrl)
                            <a href="{{ $contactWhatsAppUrl }}" target="_blank" rel="noopener noreferrer" class="btn btn-outline btn-sm bg-white text-base-content hover:bg-base-100">
                                {{ __('Help') }}
                            </a>
                        @endif
                        <button id="theme-toggle" type="button" class="btn btn-outline btn-sm">{{ __('Switch to Dark Mode') }}</button>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</body>

</html>
