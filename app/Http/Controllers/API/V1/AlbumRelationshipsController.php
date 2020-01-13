<?php

namespace App\Http\Controllers\API\V1;

use App\Models\User;
use App\Models\Album;
use App\Http\Controllers\Controller;
use App\Http\Resources\AlbumResource;
use App\Http\Resources\PhotoResource;
use App\Http\Resources\PhotoCollection;

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

	/**
	 * Returns album with user resource
	 *
	 * @param Album $album
	 * @param User $user
	 * @return AlbumResource
	 */
	public function withUser(Album $album, User $user): AlbumResource
	{
		return new AlbumResource($user->albums()->where('id', $album->id)->first(), true);
	}
}
