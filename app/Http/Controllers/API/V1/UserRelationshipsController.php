<?php

namespace App\Http\Controllers\API\V1;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use App\Http\Resources\PhotoCollection;
use App\Http\Resources\AlbumsCollection;

class UserRelationshipsController extends Controller
{
	/**
	 * Returns albums of the user
	 *
	 * @param User $user
	 * @return AlbumsCollection
	 */
	public function albums(User $user): AlbumsCollection
	{
		return new AlbumsCollection(
			Cache::remember('user'.$user->id.'albums', 60*2, function () use ($user) {
				return $user->albums;
			})
		);
	}

	/**
	 * Returns photos of the user
	 *
	 * @param User $user
	 * @return PhotoCollection
	 */
	public function photos(User $user): PhotoCollection
	{
		return new PhotoCollection(Cache::remember('user'.$user->id.'photos', 60*60*24, function () use ($user) {
				return $user->albums->flatMap(function ($album) {
						return $album->photos;
				});
		}));
	}
}
