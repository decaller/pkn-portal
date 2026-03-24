<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\DocumentResource;
use App\Http\Resources\V1\EventResource;
use App\Http\Resources\V1\NewsResource;
use App\Http\Resources\V1\TestimonialResource;
use App\Models\Document;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\News;
use App\Models\Setting;
use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    /**
     * Get aggregated content for the mobile home screen.
     */
    public function index(): JsonResponse
    {
        $user = $this->resolveApiUser();

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

        $featuredDocuments = Document::query()
            ->where('is_active', true)
            ->featured()
            ->inRandomOrder()
            ->limit(5)
            ->get();

        $registrationQuery = EventRegistration::query();
        if ($user) {
            $registrationQuery->where('booker_user_id', $user->getKey());
        }

        $alerts = $user
            ? $user->unreadNotifications()
                ->latest()
                ->limit(5)
                ->get()
                ->map(fn ($notification) => [
                    'id' => $notification->id,
                    'type' => data_get($notification->data, 'type', 'info'),
                    'title' => data_get($notification->data, 'title', class_basename($notification->type)),
                    'message' => data_get($notification->data, 'message', ''),
                    'action_route' => data_get($notification->data, 'action_route'),
                ])
                ->values()
            : collect();

        return response()->json([
            'featured_events' => EventResource::collection($featuredEvents),
            'latest_news' => NewsResource::collection($latestNews),
            'testimonials' => TestimonialResource::collection($testimonials),
            'featured_documents' => DocumentResource::collection($featuredDocuments),
            'contact_info' => [
                'phone' => Setting::defaultContactNumber(),
                'whatsapp_url' => Setting::defaultContactWhatsAppUrl(),
            ],
            'alerts' => $alerts,
            'stats' => [
                'active_registrations' => (clone $registrationQuery)
                    ->where('status', '!=', 'cancelled')
                    ->count(),
                'pending_payments' => (clone $registrationQuery)
                    ->whereIn('payment_status', ['unpaid', 'submitted'])
                    ->count(),
            ],
        ]);
    }

    private function resolveApiUser(): ?User
    {
        /** @var User|null */
        return auth('sanctum')->user();
    }
}
