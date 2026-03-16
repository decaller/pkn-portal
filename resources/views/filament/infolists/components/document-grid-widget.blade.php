<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">
    @php
        $state = $getState() ?? [];
        $items = $state['items'] ?? [];
        $empty = $state['empty'] ?? __('No documents found.');
        $prevUrl = $state['prev_url'] ?? null;
        $nextUrl = $state['next_url'] ?? null;
        $eventUrl = $state['event_url'] ?? null;
        $eventLabel = $state['event_label'] ?? __('View Related Event');
    @endphp

    @if ($eventUrl)
        <div class="mb-4 flex justify-end">
            <x-filament::button tag="a" href="{{ $eventUrl }}" size="sm" color="info" icon="heroicon-o-link">
                {{ $eventLabel }}
            </x-filament::button>
        </div>
    @endif

    @if (count($items) === 0)
        <div class="text-sm italic text-gray-500 dark:text-gray-400">
            {{ $empty }}
        </div>
    @else
        <div class="grid grid-cols-2 gap-6 md:grid-cols-3">
            @foreach ($items as $item)
                @php
                    $coverImage = $item['cover_image'] ?? null;
                    $filePath = $item['file_path'] ?? null;
                    $mimeType = $item['mime_type'] ?? '';
                    $isImage = str_contains($mimeType, 'image');
                @endphp
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 h-full overflow-hidden transition-all hover:ring-2 hover:ring-primary-500 group flex flex-col">
                    <a href="{{ $item['view_url'] ?? '#' }}" class="block w-full h-40 relative overflow-hidden">
                        @if ($coverImage)
                            <img src="{{ Storage::disk('public')->url($coverImage) }}" alt="{{ $item['title'] }}"
                                class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                        @elseif ($isImage && $filePath)
                            <img src="{{ Storage::disk('public')->url($filePath) }}" alt="{{ $item['title'] }}"
                                class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-gray-50 dark:bg-gray-900 text-gray-400">
                                @if (str_contains($mimeType, 'pdf'))
                                    <x-heroicon-o-document-text class="w-16 h-16 opacity-50" />
                                @elseif (str_contains($mimeType, 'word'))
                                    <x-heroicon-o-document-duplicate class="w-16 h-16 opacity-50" />
                                @elseif (str_contains($mimeType, 'excel') || str_contains($mimeType, 'sheet'))
                                    <x-heroicon-o-table-cells class="w-16 h-16 opacity-50" />
                                @elseif (str_contains($mimeType, 'powerpoint') || str_contains($mimeType, 'presentation'))
                                    <x-heroicon-o-presentation-chart-bar class="w-16 h-16 opacity-50" />
                                @else
                                    <x-heroicon-o-document class="w-16 h-16 opacity-50" />
                                @endif
                            </div>
                        @endif
                        <div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-colors duration-300"></div>
                    </a>

                    <div class="p-4 flex flex-col flex-1">
                        <div class="flex-1">
                            <a href="{{ $item['view_url'] ?? '#' }}"
                                class="text-lg font-bold text-primary-600 dark:text-primary-400 hover:underline line-clamp-2">
                                {{ $item['title'] ?? '-' }}
                            </a>
                            <div class="mt-1 text-sm text-gray-500 dark:text-gray-400 capitalize">
                                {{ $item['type'] ?? __('Other File') }}
                            </div>
                            @if (!empty($item['event']))
                                <div class="mt-2 text-xs text-gray-600 dark:text-gray-300">
                                    {{ __('Event') }}: {{ $item['event'] }}
                                </div>
                            @endif
                        </div>

                        <div class="mt-4 flex items-center gap-2">
                            @if (!empty($item['view_url']))
                                <x-filament::button tag="a" href="{{ $item['view_url'] }}" size="sm" color="primary"
                                    icon="heroicon-o-eye">
                                    {{ __('View') }}
                                </x-filament::button>
                            @endif
                            @if (!empty($item['download_url']))
                                <x-filament::button tag="a" href="{{ $item['download_url'] }}" size="sm" color="success"
                                    icon="heroicon-o-arrow-down-tray">
                                    {{ __('Download') }}
                                </x-filament::button>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @if ($prevUrl || $nextUrl)
            <div class="mt-4 flex items-center justify-between">
                <div>
                    @if ($prevUrl)
                        <x-filament::button tag="a" href="{{ $prevUrl }}" size="sm" color="gray" icon="heroicon-o-arrow-left">
                            {{ __('Previous') }}
                        </x-filament::button>
                    @endif
                </div>
                <div>
                    @if ($nextUrl)
                        <x-filament::button tag="a" href="{{ $nextUrl }}" size="sm" color="gray" icon="heroicon-o-arrow-right">
                            {{ __('Next') }}
                        </x-filament::button>
                    @endif
                </div>
            </div>
        @endif
    @endif
</x-dynamic-component>