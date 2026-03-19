<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\NewsResource;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class NewsController extends Controller
{
    /**
     * List all published news.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $news = News::query()
            ->where('is_published', true)
            ->when($request->search, function ($query, $search) {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate($request->integer('per_page', 15));

        return NewsResource::collection($news);
    }

    /**
     * Show a specific news article.
     */
    public function show(News $news): NewsResource
    {
        abort_unless($news->is_published, 404);

        $news->loadMissing('event');

        return new NewsResource($news);
    }
}
