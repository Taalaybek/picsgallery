<?php

namespace App\Http\Controllers\API\V1;

use App\Models\Album;
use App\Models\Photo;
use App\Traits\Uploadable;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\PhotoResource;
use App\Http\Resources\PhotoCollection;
use App\Http\Requests\PhotoStoreRequest;

class PhotoController extends Controller
{
	use Uploadable;

	/**
	 * Display a listing of the resource.
	 *
	 * @return PhotoCollection
	 */
	public function index(): PhotoCollection
	{
		return new PhotoCollection(Photo::latest()->paginate(12));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param PhotoStoreRequest $request
	 * @param Album $album
	 * @return PhotoResource
	 */
	public function store(PhotoStoreRequest $request, Album $album)
	{
		if (auth()->user()->can('update', $album)) {

			if ($request->has('file_name')) {
				$photo = $this->setData(
					$request->file('file'),
					$album,
					$request->get('file_name')
				)->toUpload()
					->makeThumbnail('small', 350)
					->makeThumbnail('medium', 400)
					->save();
			} else {
				$photo = $this->setData(
					$request->file('file'),
					$album
				)->toUpload()
					->makeThumbnail('small', 350)
					->makeThumbnail('medium', 400)
					->save();
			}

			return new PhotoResource($album->photos()->save($photo));
		}

		return response()->json(['message' => 'You need access to do this action'], 401);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param Photo $photo
	 * @return PhotoResource
	 */
	public function show(Photo $photo): PhotoResource
	{
		return new PhotoResource($photo);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param Request $request
	 * @param Photo $photo
	 * @return PhotoResource
	 */
	public function update(Request $request, Photo $photo): PhotoResource
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param Photo $photo
	 * @return JsonResponse
	 * @throws \Exception
	 */
	public function destroy(Photo $photo): JsonResponse
	{
		if (auth()->user()->can('delete', $photo)) {
			$photo->delete();

			return response()->json(['message' => 'Successfully deleted the photo resource'], 200);
		}

		return response()->json(['message' => 'You need access to do this action'], 401);
	}
}
