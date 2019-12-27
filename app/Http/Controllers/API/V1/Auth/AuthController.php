<?php

namespace App\Http\Controllers\API\V1\Auth;

use App\Http\Resources\UserResource;
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
}
