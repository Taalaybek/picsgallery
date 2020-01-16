<?php

namespace App\Http\Resources\Photo;

use App\Http\Resources\SimpleUserResource;
use App\Http\Resources\SimpleAlbumResource;
use Illuminate\Http\Resources\Json\JsonResource;

class PhotoResource extends JsonResource
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
			'type' => 'photos',
			'id' => $this->id,
			'attributes' => [
				'name' => $this->when(!is_null($this->name), $this->name),
				'base_name' => $this->base_name,
				'full_name' => $this->full_name,
				'mime_type' => $this->mime_type,
				'size' => $this->size,
				'original_file_path' => $this->original_file_path,
				'thumbnails' => [
					'small' => $this->small,
					'medium' => $this->medium
				]
			],
			'relationships' => new PhotoRelationshipResource($this, SimpleAlbumResource::class, SimpleUserResource::class),
			'links' => [
				'self' => route('photos.show', ['photo' => $this->id])
			]
		];
	}
}
