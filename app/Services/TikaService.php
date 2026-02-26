<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TikaService
{
    protected string $baseUrl;

    public function __construct()
    {
        // 'tika' is the service name in compose.yaml for Docker/Sail.
        $this->baseUrl = config("services.tika.url", "http://tika:9998");
    }

    public function extractText(string $fileContent): ?array
    {
        try {
            // We use the /rmeta/text endpoint to get both Content and Metadata
            $response = Http::withBody(
                $fileContent,
                "application/octet-stream",
            )->put("{$this->baseUrl}/rmeta/text");

            if ($response->successful()) {
                $data = $response->json()[0]; // Tika returns an array of metadata objects
                
                $content = $data["X-TIKA:content"] ?? null;
                if ($content) {
                    $content = preg_replace("/\n{3,}/", "\n\n", trim($content));
                }

                return [
                    "content" => $content,
                    "mime_type" => $data["Content-Type"] ?? null,
                    "metadata" => $data,
                ];
            }
        } catch (\Exception $e) {
            Log::error("Tika extraction failed: " . $e->getMessage());
        }

        return null;
    }
}
