<?php

namespace App\Http\Controllers\API\V1;

use App\Models\Album;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\PhotoResource;
use App\Http\Resources\PhotoCollection;
use App\Http\Resources\CreatorIdentifierResource;

class AlbumRelationshipsController extends Controller
{
    /**
     * Returns the album's photos
     *
     * @param  Album $album
     * @return PhotoCollection
     */
    public function photos(Album $album): PhotoCollection
    {
        return new PhotoCollection($album->photos()->oldest()->get());
    }

    /**
     * Returns oldest photo of the album
     *
     * @param  Album $album
     * @return PhotoResource
     */
    public function oldestPhoto(Album $album): PhotoResource
    {
        return new PhotoResource($album->photos()->oldest()->first());
    }
}
