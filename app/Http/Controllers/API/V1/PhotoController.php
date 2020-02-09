<?php

namespace App\Http\Controllers\API\V1;

use App\Models\Album;
use App\Models\Photo;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Services\AlbumsPhotoResolver;
use App\Services\PhotoResolverService;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\PhotoStoreRequest;
use App\Http\Requests\PhotoUpdateRequest;
use App\Http\Resources\Photo\PhotoResource;
use App\Http\Resources\Photo\PhotoCollection;
use App\Http\Resources\Photo\SimplePhotoResource;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;

class PhotoController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return PhotoCollection
   */
  public function index(): PhotoCollection
  {
    return new PhotoCollection(Photo::whereHas('album')->latest()->paginate(12));
  }

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param PhotoStoreRequest $request
	 * @param AlbumsPhotoResolver $photoResolver
	 * @param Album $album
	 * @return PhotoResource|JsonResponse
	 */
  public function store(PhotoStoreRequest $request, AlbumsPhotoResolver $photoResolver, Album $album)
  {
    if (auth()->user()->can('update', $album)) {

      if ($request->has('file_name')) {

        $photo = $photoResolver->setData(
          $request->file('file'),
          $album,
          $request->get('file_name')
        )->toUpload()
          ->makeThumbnail('small', 350)
          ->makeThumbnail('medium', 400)
          ->save();

      } else {

        $photo = $photoResolver->setData($request->file('file'), $album)
          ->toUpload()
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
	 * @param PhotoUpdateRequest $request
	 * @param AlbumsPhotoResolver $photoResolver
	 * @param Photo $photo
	 * @return PhotoResource|JsonResponse
	 */
  public function update(PhotoUpdateRequest $request, AlbumsPhotoResolver $photoResolver, Photo $photo): PhotoResource
  {
    if (auth()->user()->can('update', $photo)) {
      if ($request->hasFile('file') && $request->file('file')->isValid()) {
        // delete photos if file exists
        $this->deletePhotos($photo);

        $data = $photoResolver->setMutableData($photo, $request)
          ->toUpload()
          ->makeThumbnail('small', 350)
          ->makeThumbnail('medium', 400)
          ->getData();

      } else {
        $data = $photoResolver->setMutableData($photo, $request)->getData();
      }
      $photo->update($data);
      return new PhotoResource($photo);
    }

    return response()->json(['message' => 'You need access to do this action'], 401);
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

  /**
   * Deletes saved photo files
   *
   * @param Photo $photo
   * @return void
   */
  private function deletePhotos(Photo $photo): void
  {
		Storage::delete([
			$photo->original_file_path,
			$photo->medium,
			$photo->small
		]);
  }

	/**
	 * Adds temporary photo file
	 *
	 * @param PhotoStoreRequest $request
	 * @param PhotoResolverService $photoResolver
	 * @return SimplePhotoResource
	 */
  public function addTempPhoto(PhotoStoreRequest $request, PhotoResolverService $photoResolver)
  {
    if ($request->has('file_name')) {
      $photo = $photoResolver->setData(
        $request->file('file'),
        \auth()->user(),
        $request->get('file_name'),
        'temp'
      )->toUpload()->makeThumbnail('small', 350)->makeThumbnail('medium', 400)->save();

      return new SimplePhotoResource(auth()->user()->photos()->save($photo));
    }

    $photo = $photoResolver->setData(
      $request->file('file'),
      \auth()->user(),
      null,
      'temp'
    )->toUpload()->makeThumbnail('small', 350)->makeThumbnail('medium', 400)->save();

    return new SimplePhotoResource(auth()->user()->photos()->save($photo));
  }
}
