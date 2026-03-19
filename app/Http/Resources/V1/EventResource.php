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
            'cover_image' => $this->resource->cover_image ? Storage::url($this->resource->cover_image) : null,
            'photos' => collect($this->resource->photos ?? [])
                ->map(fn (string $photo) => Storage::url($photo))
                ->toArray(),
            'files' => collect($this->resource->files ?? [])
                ->map(fn (string $file) => Storage::url($file))
                ->toArray(),
            'documentation' => collect($this->resource->documentation ?? [])
                ->map(fn (string $doc) => Storage::url($doc))
                ->toArray(),
            'is_published' => $this->resource->is_published,
            'allow_registration' => $this->resource->allow_registration,
            'max_capacity' => $this->resource->max_capacity,
            'available_spots' => $this->resource->availableSpots(),
            'is_full' => $this->resource->isFull(),
            'registration_packages' => $this->resource->registration_packages, // JSON array
            'rundown' => $this->resource->rundown, // JSON array
            'tags' => $this->resource->tags,
            'proposal' => $this->resource->proposal ? Storage::url($this->resource->proposal) : null,
            'testimonials' => TestimonialResource::collection($this->whenLoaded('approvedTestimonials')),
        ];
    }
}
