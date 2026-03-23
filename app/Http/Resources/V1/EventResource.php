<?php

namespace App\Http\Resources\V1;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

/**
 * @property-read Event $resource
 */
class EventResource extends JsonResource
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
            'slug' => $this->resource->slug,
            'summary' => $this->resource->summary, // from add_expansion_columns_to_events_table
            'description' => $this->resource->description,
            'event_date' => $this->resource->event_date?->format('Y-m-d'),
            'city' => $this->resource->city,
            'province' => $this->resource->province,
            'nation' => $this->resource->nation,
            'duration_days' => $this->resource->duration_days,
            'google_maps_url' => $this->resource->google_maps_url,
            'cover_image' => $this->absoluteUrl($this->resource->cover_image),
            'photos' => collect($this->resource->photos ?? [])
                ->map(fn (string $photo) => $this->absoluteUrl($photo))
                ->toArray(),
            'files' => collect($this->resource->files ?? [])
                ->map(fn (string $file) => $this->absoluteUrl($file))
                ->toArray(),
            'documentation' => collect($this->resource->documentation ?? [])
                ->map(fn (string $doc) => $this->absoluteUrl($doc))
                ->toArray(),
            'is_published' => $this->resource->is_published,
            'allow_registration' => $this->resource->allow_registration,
            'max_capacity' => $this->resource->max_capacity,
            'available_spots' => $this->resource->availableSpots(),
            'is_full' => $this->resource->isFull(),
            'registration_packages' => $this->resource->registration_packages, // JSON array
            'rundown' => $this->resource->rundown, // JSON array
            'tags' => $this->resource->tags,
            'proposal' => $this->absoluteUrl($this->resource->proposal),
            'testimonials' => TestimonialResource::collection($this->whenLoaded('approvedTestimonials')),
        ];
    }
}
