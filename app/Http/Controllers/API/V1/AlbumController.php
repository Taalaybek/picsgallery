<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Album;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\AlbumResource;
use App\Http\Resources\AlbumsCollection;

class AlbumController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        //
    }

    /**
     * Returns albums of the current user
     * @return AlbumsCollection
     */
    public function creatorAlbums()
    {

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
     * Restore the specified destroyed album from storage.
     * 
     * @param $id
     * @return JsonResponse
     */
    public function restore($id): JsonResponse
    {
        //
    }

    /**
     * Force delete the specified album from storage.
     *
     * @param $id
     * @return JsonResponse
     */
    public function forceDelete($id): JsonResponse
    {
        //
    }
}
