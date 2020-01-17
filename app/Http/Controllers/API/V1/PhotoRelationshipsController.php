<?php

namespace App\Http\Controllers\API\V1;

use App\Models\Photo;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Cache;
use App\Http\Resources\Album\AlbumResource;

class PhotoRelationshipsController extends Controller
{
	/**
	 * Return photo's album
	 *
	 * @param  Photo $photo
	 * @return AlbumResource
	 */
	public function album(Photo $photo): AlbumResource
	{
		return new AlbumResource(Cache::remember("photo.id:{$photo->id}.album", 60*60*24, function () use ($photo) {
				return $photo->album;
		}));
	}

	/**
	 * Returns creator resource of the photo
	 *
	 * @param Photo $photo
	 * @return UserResource
	 */
	public function creator(Photo $photo): UserResource
	{
		return new UserResource($photo->album->creator);
	}
}
