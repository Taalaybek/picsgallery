<?php

namespace App\Http\Controllers\API\V1;

use App\Models\User;
use App\Models\Album;
use App\Models\Photo;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\AlbumResource;
use App\Http\Resources\CreatorIdentifierResource;

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
		return new AlbumResource($photo->album);
	}
}
