<?php

namespace App\Http\Controllers\API\V1\Auth;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class LoginController extends Controller
{
    /**
     * @param Request $request
     * @return mixed
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string|min:6',
            'password' => 'required|string|min:8'
        ]);

        $field = filter_var($request->username, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        if (Auth::attempt([$field => $request->username, 'password' => $request->password, 'active' => true])) {
            $data = [
                'grant_type' => 'password',
                'client_id' => config('services.passport.client_id'),
                'client_secret' => config('services.passport.client_secret'),
                'username' => $request->username,
                'password' => $request->password
            ];

            $request = Request::create(config('services.passport.endpoint'), 'POST', $data);

            return app()->handle($request);
        } else {
            return response()->json(['message' => 'Sent data is invalid'], 401);
        }
    }
}
