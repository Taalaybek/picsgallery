<?php

namespace App\Http\Resources\Creator;

use Illuminate\Http\Resources\Json\JsonResource;

class SimpleUserResource extends JsonResource
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
			'type' => 'users',
			'id' => $this->id,
			'attributes' => [
				'name' => $this->name,
				'email' => $this->email,
				'username' => $this->username
			],
			'links' => [
				'self' => route('users.show', ['user' => $this->id])
			]
		];
	}
}
