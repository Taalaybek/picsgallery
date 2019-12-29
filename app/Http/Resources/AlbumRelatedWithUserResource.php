<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AlbumRelatedWithUserResource extends JsonResource
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
            'type' => 'albums',
            'id' => $this->id,
            'attributes' => [
                'name' => $this->name,
                'description' => $this->description
            ],
            'relationships' => [
                'user' => new UserResource($this->creator)
            ]
        ];
    }

    public function with($request)
    {
        return [
            'self' => [
                'links' => route('albums.show.withUser', ['album' => $this->id, 'user' => $this->creator_id])
            ]
        ];
    }
}
