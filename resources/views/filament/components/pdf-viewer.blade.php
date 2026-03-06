<div class="w-full">
    @if ($getState())
        <iframe src="{{ asset('storage/' . $getState()) }}" class="w-full h-[500px] border-none rounded-lg bg-gray-100 dark:bg-gray-800">
        </iframe>
    @else
        <p class="text-sm text-gray-500">No document available.</p>
    @endif
</div>