<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\CreatorIdentifierResource;

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
				'data' => new CreatorIdentifierResource($this->creator)
			],
			'photos' => [
				'links' => ['self' => route('photos.index')],
				'data' => PhotoIdentifierResource::collection($this->photos)
			]
		];
	}
}
