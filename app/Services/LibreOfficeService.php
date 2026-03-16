<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LibreOfficeService
{
    protected string $baseUrl;

    public function __construct()
    {
        // 'libreoffice' is the service name in compose.yaml for Docker/Sail.
        $this->baseUrl = rtrim(config('services.libreoffice.url', 'http://libreoffice:9980'), '/');
    }

    public function convertToPng(string $fileContent, string $filename): ?string
    {
        try {
            $response = Http::timeout(120)
                ->attach('data', $fileContent, $filename)
                ->post("{$this->baseUrl}/cool/convert-to/png");

            if ($response->successful()) {
                return $response->body();
            }

            Log::warning('LibreOffice returned non-successful response: '.$response->status());
        } catch (\Exception $e) {
            Log::error('LibreOffice conversion failed: '.$e->getMessage());
        }

        return null;
    }
}
