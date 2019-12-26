<?php

namespace App\Http\Controllers\API\V1\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AccountActivationController extends Controller
{
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
}
