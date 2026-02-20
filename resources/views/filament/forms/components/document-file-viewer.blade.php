<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    <div x-data="{ state: $wire.$entangle('{{ $getStatePath() }}') }" class="overflow-hidden rounded-lg border bg-gray-50 dark:bg-gray-900">
        @php
            $state = $getState();

            $url = null;

            if (filled($state)) {
                $stateString = (string) $state;

                if (str_starts_with($stateString, 'http://') || str_starts_with($stateString, 'https://') || str_starts_with($stateString, '/')) {
                    $url = $stateString;
                } else {
                    $url = \Illuminate\Support\Facades\Storage::disk('public')->url($stateString);
                }
            }

            $ext = strtolower(pathinfo((string) $url, PATHINFO_EXTENSION));
        @endphp

        @if (! $url)
            <div class="p-4 text-center text-sm italic text-gray-500">No file available for preview.</div>
        @elseif (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp'], true))
            <img src="{{ $url }}" class="mx-auto h-auto max-h-[500px] w-full object-contain" />
        @elseif ($ext === 'pdf')
            <iframe src="{{ $url }}" class="block w-full" style="display: block; width: 100%; min-width: 1000px; min-height: 1000px; height: 1000px;" frameborder="0"></iframe>
        @elseif (in_array($ext, ['doc', 'docx', 'xls', 'xlsx', 'pptx'], true))
            <iframe src="https://view.officeapps.live.com/op/view.aspx?src={{ urlencode($url) }}" class="block w-full" style="display: block; width: 100%; min-width: 1000px; min-height: 1000px; height: 1000px;" frameborder="0"></iframe>
        @else
            <div class="p-6 text-center">
                <p class="mb-4 text-sm text-gray-600">Preview not supported for <strong>.{{ $ext ?: 'file' }}</strong></p>
                <a href="{{ $url }}" target="_blank" class="rounded-lg bg-primary-600 px-4 py-2 text-sm text-white shadow">
                    Download File
                </a>
            </div>
        @endif
    </div>
</x-dynamic-component>
