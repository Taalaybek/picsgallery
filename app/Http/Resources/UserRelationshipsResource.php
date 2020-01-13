<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserRelationshipsResource extends JsonResource
{
	/**
		* Transform the resource into an array.
		*
		* @param  \Illuminate\Http\Request  $request
		* @return array
		*/
	public function toArray($request)
	{
		$photos = $this->albums->flatMap(
			function ($album) {
				return $album->photos;
			}
		);

		return [
			'albums' => [
				'links' => [
					'self' => route('albums.index'),
					'related' => route('albums.userAlbums', ['user' => $this->id])
				],
				'data' => AlbumIdentifierResource::collection($this->albums)
			],
			'photos' => [
				'links' => [
					'self' => route('photos.index'),
					'related' => route('users.relationship.photos', ['user' => $this->id])
				],
				'data' => PhotoIdentifierResource::collection($photos)
			]
		];
	}
}
