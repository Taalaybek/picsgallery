<?php

namespace App\Http\Controllers\API\V1\Auth;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Returns current user resource
     *
     * @return UserResource
     */
    public function user(): UserResource
    {
        return new UserResource(Auth::user());
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function refreshToken(Request $request)
    {
        $request->validate(['refresh_token' => 'required|string|min:60']);

        $data = [
            'grant_type' => 'refresh_token',
            'refresh_token' => $request->input('refresh_token'),
            'client_id' => config('services.passport.client_id'),
            'client_secret' => config('services.passport.client_secret'),
            'scope' => '',
        ];

        // post request to oauth/token with refresh_token grant type
        $request = Request::create(config('services.passport.endpoint'), 'POST', $data);

        return app()->handle($request);
    }

    /**
     * @param string $token
     * @return \Illuminate\Http\JsonResponse
     */
    public function activateToken(string $token)
    {
        $user = User::where('activation_token', $token)->first();

        if (!$user) {
            return response()->json([
                'message' => 'The activation token is invalid'
            ], 404);
        }

        $user->update(['active' => true, 'activation_token' => '', 'email_verified_at' => now()]);

        return response()->json(['data' => $user], 200);
    }

    /**
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthorized'], 429);
        }

        auth()->user()->token()->revoke();

        return response()->json(['message' => 'Successfully logged out'], 200);
    }
}
