<?php

namespace App\Http\Controllers\API\V1;

use App\Models\Album;
use Illuminate\Http\Request;
use App\Events\AlbumDeletedEvent;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use App\Events\AlbumCreatedWithPhotos;
use App\Http\Resources\Album\AlbumResource;
use App\Http\Resources\Album\AlbumsCollection;

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
			return Album::whereHas('photos')->with('creator')->latest()->paginate(12);
		}));
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
				'description' => 'sometimes|string|min:10|max:400',
				'photos' => 'sometimes|array'
		]);

		$request->merge(['creator_id' => auth()->user()->id]);

		$album = Album::create($request->except('photos'));

		if ($request->has('photos')) {
			event(new AlbumCreatedWithPhotos($album, $request->get('photos')));
		}

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
	 * @return AlbumResource|JsonResponse
	 */
	public function update(Request $request, Album $album)
	{
		if (auth()->user()->can('update', $album)) {
			$request->validate([
				'name' => 'sometimes|string|min:6|max:255',
				'description' => 'sometimes|string|min:10|max:400'
			]);

			$album->update($request->all());

			return new AlbumResource($album);
		}

		return response()->json(['message' => 'You need access to do this action'], 401);
	}

	/**
		* Remove the specified resource from storage.
		*
		* @param Album $album
		* @return JsonResponse
		* @throws \Exception
		*/
	public function destroy(Album $album)
	{
		if (auth()->user()->can('delete', $album)) {
			$album->delete();
			event(new AlbumDeletedEvent());
			return response()->json(['message' => 'Successfully deleted the album'], 200);
		}

		return \response()->json(['message' => 'You need access to do this action'], 401);
	}
}
