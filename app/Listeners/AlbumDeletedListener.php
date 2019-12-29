<?php

namespace App\Listeners;

use App\Events\AlbumDeletedEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AlbumDeletedListener implements ShouldQueue
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
     * @param AlbumDeletedEvent $event
     * @return void
     */
    public function handle(AlbumDeletedEvent $event)
    {
        Cache::put('albums.index', DB::table('albums')->latest()->paginate(12), 60*24);
    }
}
