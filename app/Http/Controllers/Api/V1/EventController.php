<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\EventResource;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class EventController extends Controller
{
    /**
     * List all published events.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $events = Event::query()
            ->where('is_published', true)
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->when($request->category, function ($query, $category) {
                $query->where('event_type', $category);
            })
            ->when($request->status, function ($query, $status) {
                if ($status === 'open') {
                    $query->where('allow_registration', true);
                } elseif ($status === 'closed') {
                    $query->where('allow_registration', false);
                }
            })
            ->latest('event_date')
            ->paginate($request->integer('per_page', 15));

        return EventResource::collection($events);
    }

    /**
     * Show a specific event.
     */
    public function show(Event $event): EventResource
    {
        abort_unless($event->is_published, 404);

        $event->loadMissing(['approvedTestimonials.user']);

        return new EventResource($event);
    }

    /**
     * Show similar events.
     */
    public function similar(Event $event): AnonymousResourceCollection
    {
        $similar = Event::query()
            ->where('is_published', true)
            ->where('id', '!=', $event->id)
            ->where('event_type', $event->event_type) // Filter by type if available
            ->latest('event_date')
            ->limit(5)
            ->get();

        return EventResource::collection($similar);
    }
}
