<?php

namespace App\Listeners;

use App\Events\AlbumCreatedWithPhotos;
use App\Models\Photo;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AlbumCreatedListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

	/**
	 * Handle the event.
	 *
	 * @param AlbumCreatedWithPhotos $event
	 * @return void
	 */
    public function handle(AlbumCreatedWithPhotos $event)
    {
        foreach ($event->photos as $photo) {
        	$photo = Photo::find($photo);

        	\Storage::move(
        		$photo->original_file_path,
						'albums/'.$event->album->id.'/'.$photo->full_name
					); // move original
        	\Storage::move(
        		$photo->small,
						'albums/'.$event->album->id.'/'.'thumbnails/'.basename($photo->small)
					); // move small size thumbnail
        	\Storage::move(
        		$photo->medium,
						'albums/'.$event->album->id.'/'.'thumbnails/'.basename($photo->medium)
					); // move medium size thumbnail

        	$photo->update([
        		'album_id' => $event->album->id,
						'original_file_path' => 'albums/'.$event->album->id.'/'.$photo->full_name,
						'thumbnails' => [
							'small' => [
								'path' => 'albums/'.$event->album->id.'/'.'thumbnails/'.basename($photo->small),
							],
							'medium' => [
								'path' => 'albums/'.$event->album->id.'/'.'thumbnails/'.basename($photo->medium)
							]
						]
					]);
				}
    }
}
