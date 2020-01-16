<?php

namespace App\Http\Resources;

use App\Http\Resources\SimplePhotoResource;
use Illuminate\Http\Resources\Json\JsonResource;

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
