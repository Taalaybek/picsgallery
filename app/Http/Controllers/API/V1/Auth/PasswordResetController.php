<?php

namespace App\Http\Controllers\API\V1\Auth;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\PasswordReset;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Notifications\PasswordResetRequest;
use App\Notifications\PasswordResetSuccess;
use App\Http\Requests\PasswordResetValidate;

class PasswordResetController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function create(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->get('email'))->first();

        if (!$user) {
            return response()->json(['message' => 'We can\'t find a user with that e - mail address .'], 404);
        }

        $passwordReset = PasswordReset::updateOrCreate(
            ['email' => $user->email],
            ['email' => $user->email, 'token' => Str::random(60)]
        );

        if ($user && $passwordReset) {
            $user->notify(new PasswordResetRequest($passwordReset->token));
        }

        return response()->json(['message' => 'We have e-mailed your password reset link!'], 200);

    }

    /**
     * @param $token
     * @return JsonResponse
     */
    public function find($token): JsonResponse
    {
        $passwordReset = PasswordReset::where('token', $token)->first();

        if (!$passwordReset) {
            return response()->json(['message' => 'This password reset token is invalid.'], 404);
        }

        if (Carbon::parse($passwordReset->updated_at)->addMinutes(720)->isPast()) {
            $passwordReset->delete();
            return response()->json(['message' => 'This password reset token is invalid.'], 200);
        }

        return response()->json($passwordReset);
    }

    /**
     * @param PasswordResetValidate $request
     * @return JsonResponse
     */
    public function updatePassword(PasswordResetValidate $request)
    {
        $passwordReset = PasswordReset::where([
            'email' => $request->email,
            'token' => $request->token
        ])->first();

        if (!$passwordReset) {
            return response()->json([
                'message' => 'This password reset token is invalid.'
            ], 404);
        }

        $user = User::where('email', $passwordReset->email)->first();

        if (!$user) {
            return response()->json([
                'message' => 'We can\'t find a user with that e-mail address.'
            ], 404);
        }

        $user->password = bcrypt($request->password);
        $user->save();

        $passwordReset->delete();

        $user->notify(new PasswordResetSuccess());

        return response()->json($user);
    }
}
