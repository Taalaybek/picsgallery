<?php

namespace App\Http\Controllers\API\V1;

use App\Models\User;
use App\Models\Album;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use App\Http\Resources\AlbumResource;
use App\Http\Resources\AlbumsCollection;
use App\Http\Resources\AlbumRelatedWithUserResource;

class AlbumController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return AlbumsCollection
     */
    public function index(): AlbumsCollection
    {
        return new AlbumsCollection(Cache::remember('albums.index', 60, function () {
            return DB::table('albums')->latest()->paginate(12);
        }));
    }

    /**
     * Returns albums of the current user
     * @return AlbumsCollection
     */
    public function creatorAlbums(): AlbumsCollection
    {
        return new AlbumsCollection(auth()->user()->albums()->paginate(12));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return AlbumResource
     */
    public function store(Request $request): AlbumResource
    {
        $request->validate([
            'name' => 'required|string|min:6|max:255',
            'description' => 'sometimes|string|min:10|max:400'
        ]);
        $request->merge(['creator_id' => auth()->user()->id]);

        $album = Album::create($request->all());

        return new AlbumResource($album);
    }

    /**
     * Display the specified resource.
     *
     * @param Album $album
     * @return AlbumResource
     */
    public function show(Album $album)
    {
        return new AlbumResource($album);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Album $album
     * @return Response
     */
    public function update(Request $request, Album $album)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Album $album
     * @return Response
     */
    public function destroy(Album $album)
    {
        //
    }

    /**
     * @param Album $album
     * @param User $user
     * @return AlbumRelatedWithUserResource
     */
    public function showWithUser(Album $album, User $user): AlbumRelatedWithUserResource
    {
        $album = Album::where(['creator_id' => $user->id, 'id' => $album->id])->first();

        return new AlbumRelatedWithUserResource($album);
    }
}
