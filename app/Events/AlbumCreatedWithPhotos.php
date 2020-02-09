<?php

namespace App\Events;

use App\Models\Album;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AlbumCreatedWithPhotos
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $album;
    public $photos;

	/**
	 * Create a new event instance.
	 *
	 * @param Album $album
	 * @param array $photos
	 */
    public function __construct(Album $album, array $photos)
    {
        $this->album = $album;
        $this->photos = $photos;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
