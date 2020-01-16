<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use App\Http\Resources\AlbumResourceForCollections;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AlbumsCollection extends ResourceCollection
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
		* @param  Request  $request
		* @return AnonymousResourceCollection
		*/
	public function toArray($request)
	{
		if (is_null($this->resourceInstance)) {
			return AlbumResourceForCollections::collection($this->collection);
		}

		if (!is_null($this->resourceInstance) && class_exists($this->resourceInstance)) {
			return $this->resourceInstance::collection($this->collection);
		}
	}

	public function with($request)
	{
		return [
			'links' => ['links' => route('albums.index')]
		];
	}
}
