<?php

namespace App\Http\Resources;

use App\Http\Resources\SimpleUserResource;
use App\Http\Resources\SimplePhotoResource;
use Illuminate\Http\Resources\Json\JsonResource;

class AlbumResource extends JsonResource
{
	protected $withCreator;
	protected $withPhotos;

	public function __construct($resource, $hasCreator = false, $hasPhotos = false)
	{
		parent::__construct($resource);

		$this->withCreator = $hasCreator;
		$this->withPhotos = $hasPhotos;
	}

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
			],
			'included' => $this->when($this->withCreator || $this->withPhotos, [
				'creator' => $this->when($this->withCreator, new SimpleUserResource($this->creator)),
				'photos' => $this->when($this->withPhotos, SimplePhotoResource::collection($this->photos))
			])
		];
	}
}
