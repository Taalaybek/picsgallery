<?php

namespace App\Http\Resources\Photo;

use Illuminate\Http\Resources\Json\JsonResource;

class PhotoRelationshipResource extends JsonResource
{
	protected $albumInstance;
	protected $creatorInstance;

	public function __construct($resource, string $albumInstance, string $creatorInstance)
	{
		parent::__construct($resource);

		if ($this->resourceExists($this->albumInstance) && $this->resourceExists($this->creatorInstance)) {
			$this->albumInstance = $albumInstance;
			$this->creatorInstance = $creatorInstance;
		} else {
			$this->albumInstance = AlbumIdentifierResource::class;
			$this->creatorInstance = CreatorIdentifierResource::class;
		}
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
			'album' => [
				'links' => [
					'self' => route('albums.show', ['album' => $this->album]),
					'related' => route('photos.relationships.album', ['photo' => $this->id])
				],
				'data' => new $this->albumInstance($this->album)
			],
			'creator' => [
				'data' => new $this->creatorInstance($this->album->creator)
			]
		];
	}

	/**
	 * Returns boolean if class exists 
	 * and class extends JsonResource
	 *
	 * @param string $class
	 * @return boolean
	 */
	private function resourceExists(string $class): bool
	{
		return class_exists($this->albumInstance) && array_key_first(class_parents($this->albumInstance)) == 'Illuminate\Http\Resources\Json\JsonResource';
	}
}
