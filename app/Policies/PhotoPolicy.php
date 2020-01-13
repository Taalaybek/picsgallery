<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Photo;
use Illuminate\Auth\Access\HandlesAuthorization;

class PhotoPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can update the photo.
     *
     * @param User $user
     * @param Photo $photo
     * @return mixed
     */
    public function update(User $user, Photo $photo)
    {
        return $user->id == $photo->album->creator_id;
    }

    /**
     * Determine whether the user can delete the photo.
     *
     * @param User $user
     * @param Photo $photo
     * @return mixed
     */
    public function delete(User $user, Photo $photo)
    {
        return $user->id == $photo->album->creator_id;
    }

}
