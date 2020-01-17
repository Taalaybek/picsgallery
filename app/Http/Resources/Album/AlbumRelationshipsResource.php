<?php

namespace App\Http\Resources\Album;

use App\Http\Resources\SimpleUserResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Photo\SimplePhotoResource;

class AlbumRelationshipsResource extends JsonResource
{
	/**
		* Transform the resource into an array.
		*
		* @param  \Illuminate\Http\Request  $request
		* @return array
		*/
	public function toArray($request)
	{
		return [
			'creator' => [
				'links' => [
					'self' => route('users.show', ['user' => $this->creator_id])
				],
				'data' => new SimpleUserResource($this->creator)
			],
			'photos' => [
				'links' => ['self' => route('photos.index')],
				'data' => SimplePhotoResource::collection($this->photos)
			]
		];
	}
}
