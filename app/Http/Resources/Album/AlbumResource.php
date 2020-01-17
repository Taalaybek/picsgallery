<?php

namespace App\Http\Resources\Album;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Album\AlbumRelationshipsResource;

class AlbumResource extends JsonResource
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
			'relationships' => new AlbumRelationshipsResource($this),
			'links' => [
				'links' => route('albums.show', ['album' => $this->id])
			]
		];
	}
}
