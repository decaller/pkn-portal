<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Authenticate a user via their phone number and password.
     */
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'phone_number' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid phone number or password.',
            ], 401);
        }

        $user = Auth::user();

        // Revoke existing mobile-app tokens if needed or just create a new one
        $user->tokens()->where('name', 'mobile-app')->delete();
        $token = $user->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'success' => true,
            'token' => $token,
            'user' => new UserResource($user),
        ]);
    }

    /**
     * Get the authenticated user.
     */
    public function me(Request $request): UserResource
    {
        return new UserResource($request->user());
    }

    /**
     * Revoke the current token.
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully.',
        ]);
    }
}
