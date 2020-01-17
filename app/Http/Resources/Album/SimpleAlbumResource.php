<?php

namespace App\Http\Resources\Album;

use Illuminate\Http\Resources\Json\JsonResource;

class SimpleAlbumResource extends JsonResource
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
				'self' => route('albums.show', ['album' => $this->id])
			]
		];
	}
}
