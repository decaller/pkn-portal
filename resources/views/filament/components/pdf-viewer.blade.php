<div class="w-full">
    @if ($getState())
        <iframe src="{{ asset('storage/' . $getState()) }}" width="100%" height="500px"
            style="border: none; border-radius: 0.5rem; background: #f3f4f6;">
        </iframe>
    @else
        <p class="text-sm text-gray-500">No document available.</p>
    @endif
</div>