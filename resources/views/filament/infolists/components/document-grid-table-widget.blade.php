<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">
    @php
        $state = $getState() ?? [];
        $recordId = $state['record_id'] ?? null;
        $mode = $state['mode'] ?? 'similar';
    @endphp

    @if ($recordId)
        @livewire(
            \App\Filament\Shared\Widgets\DocumentGridTableWidget::class,
            ['recordId' => $recordId, 'mode' => $mode],
            key('document-grid-'.$mode.'-'.$recordId)
        )
    @else
        <div class="text-sm italic text-gray-500 dark:text-gray-400">
            {{ __('No documents found.') }}
        </div>
    @endif
</x-dynamic-component>
