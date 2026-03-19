<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class WebViewController extends Controller
{
    /**
     * Generate a temporary signed URL for magic login in WebView.
     */
    public function getMagicLink(Request $request): JsonResponse
    {
        $user = $request->user();
        $redirect = $request->query('redirect', '/user');

        $url = URL::temporarySignedRoute(
            'webview.magic-login',
            now()->addMinutes(5),
            [
                'user_id' => $user->id,
                'redirect' => $redirect,
            ]
        );

        return response()->json([
            'url' => $url,
        ]);
    }

    /**
     * Handle the signed URL, log the user in, and redirect.
     */
    public function handleMagicLink(Request $request)
    {
        if (! $request->hasValidSignature()) {
            abort(403, 'Invalid or expired magic link.');
        }

        $userId = $request->query('user_id');
        $redirect = $request->query('redirect', '/user');

        auth()->loginUsingId($userId);

        return redirect($redirect);
    }
}
