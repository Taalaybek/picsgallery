<?php

namespace App\Http\Resources;

use App\Http\Resources\UserResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class UsersCollection extends ResourceCollection
{
	/**
		* Transform the resource collection into an array.
		*
		* @param  \Illuminate\Http\Request  $request
		* @return array
		*/
	public function toArray($request)
	{
		return UserResource::collection($this->collection);
	}

	public function with($request)
	{
		return [
			'links' => ['self' => route('users.index')]
		];
	}
}
