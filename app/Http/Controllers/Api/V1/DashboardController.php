<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\EventResource;
use App\Http\Resources\V1\NewsResource;
use App\Http\Resources\V1\TestimonialResource;
use App\Models\Event;
use App\Models\News;
use App\Models\Testimonial;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    /**
     * Get aggregated content for the mobile home screen.
     */
    public function index(): JsonResponse
    {
        $featuredEvents = Event::query()
            ->where('is_published', true)
            ->latest('event_date')
            ->limit(5)
            ->get();

        $latestNews = News::query()
            ->where('is_published', true)
            ->latest()
            ->limit(10)
            ->get();

        $testimonials = Testimonial::query()
            ->where('is_approved', true)
            ->latest()
            ->limit(5)
            ->get();

        return response()->json([
            'featured_events' => EventResource::collection($featuredEvents),
            'latest_news' => NewsResource::collection($latestNews),
            'testimonials' => TestimonialResource::collection($testimonials),
        ]);
    }
}
