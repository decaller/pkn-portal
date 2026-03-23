<?php

namespace App\Http\Resources\V1;

use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

/**
 * @property-read News $resource
 */
class NewsResource extends JsonResource
{
    private function absoluteUrl(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        return url(Storage::url($path));
    }

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'title' => $this->resource->title,
            'content' => $this->resource->content,
            'thumbnail' => $this->absoluteUrl($this->resource->thumbnail),
            'is_published' => $this->resource->is_published,
            'event_id' => $this->resource->event_id,
            'created_at' => $this->resource->created_at?->toIso8601String(),
            'event' => $this->whenLoaded('event', fn () => [
                'id' => $this->resource->event->id,
                'title' => $this->resource->event->title,
                'slug' => $this->resource->event->slug,
            ]),
        ];
    }
}
