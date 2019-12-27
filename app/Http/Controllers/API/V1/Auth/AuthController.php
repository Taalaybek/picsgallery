<?php

namespace App\Http\Controllers\API\V1\Auth;

use App\Http\Resources\UserResource;
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
}
