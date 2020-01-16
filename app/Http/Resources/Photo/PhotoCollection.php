<?php

namespace App\Http\Resources\Photo;

use App\Http\Resources\Photo\PhotoResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class PhotoCollection extends ResourceCollection
{
	protected $resourceInstance;

	public function __construct($resource, $resourceInstance = null)
	{
		parent::__construct($resource);
		$this->resourceInstance = $resourceInstance;
	}

	/**
	 * Transform the resource collection into an array.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
	 */
	public function toArray($request)
	{
		if (!is_null($this->resourceInstance) &&
		  class_exists($this->resourceInstance) && 
			array_key_first(class_parents($this->resourceInstance)) == 'Illuminate\Http\Resources\Json\JsonResource'
		) {
		  return $this->resourceInstance::collection($this->collection);
		}

		if (is_null($this->resourceInstance)) {
			return PhotoResource::collection($this->collection);
		}
	}

	public function with($request)
	{
		return [
			'links' => ['self' => route('photos.index')]
		];
	}
}
