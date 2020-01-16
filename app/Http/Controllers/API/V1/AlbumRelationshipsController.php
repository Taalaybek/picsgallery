<?php

namespace App\Http\Controllers\API\V1;

use App\Models\User;
use App\Models\Album;
use App\Http\Controllers\Controller;
use App\Http\Resources\PhotoResource;
use App\Http\Resources\PhotoCollection;
use App\Http\Resources\AlbumsCollection;
use App\Http\Resources\AlbumResourceForCollections;

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
	 * @return AlbumResourceForCollections
	 */
	public function withUser(Album $album, User $user): AlbumResourceForCollections
	{
		return new AlbumResourceForCollections($user->albums()->where('id', $album->id)->first());
	}

		/**
		* Returns albums of the current user
		* @return AlbumsCollection
		*/
	public function authenticatedUserAlbums(): AlbumsCollection
	{
		return new AlbumsCollection(auth()->user()->albums()->latest()->paginate(12));
	}

	/**
		* @param User $user
		* @return AlbumsCollection
		*/
	public function userAlbums(User $user): AlbumsCollection
	{
		return new AlbumsCollection($user->albums()->paginate(12));
	}
}
