<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SimplePhotoResource extends JsonResource
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
				'full_name' => $this->full_name,
				'size' => $this->size,
				'original_file_path' => $this->original_file_path,
				'thumbnails' => [
					'small' => $this->small,
					'medium' => $this->medium
				]
			],
			'links' => [
				'self' => route('photos.show', ['photo' => $this->id])
			]
		];
	}
}
