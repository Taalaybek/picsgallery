<?php

namespace App\Http\Controllers\API\V1;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use App\Http\Resources\Creator\UserResource;
use App\Http\Resources\Creator\UsersCollection;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return new UsersCollection(Cache::remember('users.index', 120*24, function () {
           return  User::paginate(12);
        }));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        return Cache::remember('users.show'.$user->id, 60*60*24, function () use ($user) {
            return new UserResource($user);
        });;
    }
}
