@php
    use App\Models\Setting;

    $whatsAppUrl = Setting::defaultContactWhatsAppUrl('Hello, I need support for PKN Portal.');
    $helpLabel = app()->getLocale() === 'id' ? 'Bantuan' : 'Help';
@endphp

@if ($whatsAppUrl)
    <x-filament::button href="{{ $whatsAppUrl }}" tag="a" target="_blank" rel="noopener noreferrer" color="gray" size="sm"
        outlined>
        {{ $helpLabel }}
    </x-filament::button>
@endif