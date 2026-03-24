<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RegistrationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        if (! $this->resource) {
            return [];
        }

        $packageBreakdown = is_array($this->resource->package_breakdown) ? $this->resource->package_breakdown : [];
        $firstPackage = $packageBreakdown[0] ?? null;

        return [
            'id' => $this->resource->id,
            'event_id' => $this->resource->event_id,
            'organization_id' => $this->resource->organization_id,
            'booker_user_id' => $this->resource->booker_user_id,
            'status' => $this->resource->status,
            'payment_status' => $this->resource->payment_status,
            'total_amount' => $this->resource->total_amount,
            'payment_proof_path' => $this->resource->payment_proof_path,
            'notes' => $this->resource->notes,
            'verified_by_user_id' => $this->resource->verified_by_user_id,
            'verified_at' => $this->resource->verified_at,
            'created_at' => $this->resource->created_at,
            'updated_at' => $this->resource->updated_at,

            // Computed fields for mobile UI
            'package_name' => $firstPackage['package_name'] ?? ($firstPackage['name'] ?? null),
            'participant_count' => $this->resource->participants_count ?? count($this->whenLoaded('participants') ?? []),
            'unit_price' => $firstPackage['unit_price'] ?? ($firstPackage['price'] ?? '0.00'),
            'package_breakdown' => collect($packageBreakdown)->map(fn ($item) => [
                'package_id' => $item['package_id'] ?? null,
                'name' => $item['name'] ?? ($item['package_name'] ?? 'Selected Package'),
                'count' => $item['count'] ?? ($item['participant_count'] ?? ($item['quantity'] ?? 0)),
                'price' => $item['price'] ?? ($item['unit_price'] ?? 0),
                'subtotal' => $item['subtotal'] ?? 0,
            ])->toArray(),

            'event' => new EventResource($this->whenLoaded('event')),
            'participants' => ParticipantResource::collection($this->whenLoaded('participants')),
        ];
    }
}
