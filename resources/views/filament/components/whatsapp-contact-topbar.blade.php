@php
    use App\Models\Setting;

    $whatsAppUrl = Setting::defaultContactWhatsAppUrl('Hello, I need support for PKN Portal.');
    $helpLabel = app()->getLocale() === 'id' ? 'Bantuan' : 'Help';
@endphp

@if ($whatsAppUrl)
    <a href="{{ $whatsAppUrl }}" target="_blank" rel="noopener noreferrer"
        class="fi-btn fi-btn-size-sm fi-color-gray fi-btn-color-gray fi-btn-outlined bg-white text-gray-900"
        style="margin-left: 10px;">
        {{ $helpLabel }}
    </a>
@endif