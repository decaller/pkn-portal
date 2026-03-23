<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\DocumentResource;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class DocumentController extends Controller
{
    /**
     * List all active documents.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $documents = Document::query()
            ->where('is_active', true)
            ->when($request->search, function ($query, $search) {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('original_filename', 'like', "%{$search}%");
            })
            ->when($request->event_id, fn ($query, $eventId) => $query->where('event_id', $eventId))
            ->when($request->boolean('is_featured'), fn ($query) => $query->featured())
            ->latest()
            ->paginate($request->integer('per_page', 20));

        return DocumentResource::collection($documents);
    }

    /**
     * Show a specific document.
     */
    public function show(Document $document): DocumentResource
    {
        abort_unless($document->is_active, 404);

        return new DocumentResource($document);
    }
}
