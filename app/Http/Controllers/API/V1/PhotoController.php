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
        //
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

            $photo = $this->setData(
                $request->file('file'),
                $request->get('file_name'),
                $album,
                'albums'
            )->toUpload()
                ->makeThumbnail('small', 350)
                ->makethumbnail('medium', 400)
                ->save();

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
        //
    }
}
