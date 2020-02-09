<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class Photo extends Model
{
    protected $table = 'photos';

    protected $fillable = [
        'name',
        'size',
        'mime_type',
        'album_id',
        'base_name',
        'thumbnails',
        'full_name',
        'original_file_path',
        'user_id'
    ];

    protected static function boot()
    {
        parent::boot();
        self::deleting(function (Photo $photo) {
            Storage::delete($photo->original_file_path);
            Storage::delete($photo->small);
            Storage::delete($photo->medium);
        });
    }

    public function album()
    {
        return $this->belongsTo(Album::class, 'album_id', 'id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function getSmallAttribute()
    {
       $thumbnails = json_decode( $this->thumbnails, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

       return $thumbnails['small']['path'];
    }

    public function getMediumAttribute()
    {
        $thumbnails = json_decode( $this->thumbnails, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        return $thumbnails['medium']['path'];
    }
}
