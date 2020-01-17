<?php

namespace App\Http\Resources\Album;

use App\Http\Resources\SimpleUserResource;
use App\Http\Resources\Photo\SimplePhotoResource;
use Illuminate\Http\Resources\Json\JsonResource;

class AlbumResourceForCollections extends JsonResource
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
			'type' => 'albums',
			'id' => $this->id,
			'attributes' => [
				'name' => $this->name,
				'description' => $this->description
			],
			'links' => [
				'links' => route('albums.show', ['album' => $this->id])
			],
			'included' => [
				'creator' => new SimpleUserResource($this->creator),
				'oldest' => $this->when(!is_null($this->photos->first()), new SimplePhotoResource($this->photos->first())),
				'count' => $this->photos->count()
			]
		];
	}
}
