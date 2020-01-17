<?php

namespace App\Http\Resources\Photo;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Album\AlbumIdentifierResource;
use App\Http\Resources\Creator\CreatorIdentifierResource;

class PhotoRelationshipResource extends JsonResource
{
	protected $albumInstance;
	protected $creatorInstance;

	public function __construct($resource, string $albumInstance = null, string $creatorInstance = null)
	{
		parent::__construct($resource);

		$this->albumInstance = $albumInstance;
		$this->creatorInstance = $creatorInstance;
	}

	/**
	 * Transform the resource into an array.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return array
	 */
	public function toArray($request)
	{
		$this->checkResourceInstance();
		
		return [
			'album' => [
				'links' => [
					'self' => route('albums.show', ['album' => $this->album]),
					'related' => route('photos.relationships.album', ['photo' => $this->id])
				],
				'data' => new $this->albumInstance($this->album)
			],
			'creator' => [
				'links' => [
					'self' => route('users.show', ['user' => $this->album->creator]),
					'related' => route('photos.relationships.creator', ['photo' => $this->id])
				],
				'data' => new $this->creatorInstance($this->album->creator)
			]
		];
	}

	/**
	 * Checks creator and album resources
	 *
	 * @return this
	 */
	private function checkResourceInstance()
	{
		if (is_null($this->creatorInstance) && is_null($this->albumInstance) || 
		!$this->resourceExists($this->albumInstance) && !$this->resourceExists($this->creatorInstance)) {
			$this->albumInstance = AlbumIdentifierResource::class;
			$this->creatorInstance = CreatorIdentifierResource::class;
		}

		return $this;
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
