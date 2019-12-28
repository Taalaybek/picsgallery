<?php

namespace App\Policies;

use App\Models\Album;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AlbumPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can update the album.
     *
     * @param User $user
     * @param Album $album
     * @return mixed
     */
    public function update(User $user, Album $album)
    {
        return $user->id == $album->creator_id;
    }

    /**
     * Determine whether the user can delete the album.
     *
     * @param User $user
     * @param Album $album
     * @return mixed
     */
    public function delete(User $user, Album $album)
    {
        return $user->id == $album->creator_id;
    }
}
