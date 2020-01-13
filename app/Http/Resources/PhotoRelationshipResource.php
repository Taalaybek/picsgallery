<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PhotoRelationshipResource extends JsonResource
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
			'album' => [
				'links' => [
					'self' => route('albums.show', ['album' => $this->album]),
					'related' => route('photos.relationships.album', ['photo' => $this->id])
				],
				'data' => new AlbumIdentifierResource($this->album)
			],
			'creator' => [
				'data' => new CreatorIdentifierResource($this->album->creator)
			]
		];
	}
}
