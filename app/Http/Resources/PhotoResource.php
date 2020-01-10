<?php

namespace App\Http\Resources;

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
				'name' => $this->name,
				'base_name' => $this->base_name,
				'full_name' => $this->full_name,
				'mime_type' => $this->mime_type,
				'size' => $this->size,
				'original_file_path' => $this->original_file_path,
				'small' => $this->small,
				'medium' => $this->medium
			],
			'relationships' => new PhotoRelationshipResource($this),
			'links' => [
				'self' => route('photos.show', ['photo' => $this->id])
			]
		];
	}
}
