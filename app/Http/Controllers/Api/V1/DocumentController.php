<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\DocumentResource;
use App\Models\Document;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    /**
     * List all active documents.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Document::query()
            ->where('is_active', true)
            ->when($request->search, function ($query, $search) {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('original_filename', 'like', "%{$search}%");
            })
            ->when($request->filled('category'), function ($query) use ($request) {
                $query->whereJsonContains('tags', $request->string('category')->toString());
            })
            ->when($request->boolean('is_featured'), function ($query) {
                $query->featured();
            })
            ->when($request->event_id, fn ($query, $eventId) => $query->where('event_id', $eventId));

        // Get featured documents (limit to 10)
        $featuredDocuments = (clone $query)->featured()->latest()->limit(10)->get();

        // Get all active documents
        $documents = $query->latest()->get();

        return response()->json([
            'featured_documents' => DocumentResource::collection($featuredDocuments),
            'documents' => [
                'data' => DocumentResource::collection($documents),
            ],
        ]);
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
