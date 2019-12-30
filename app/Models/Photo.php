<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Photo extends Model
{
    protected $table = 'photos';

    protected $fillable = [
        'name', 'path', 'size', 'mime_type', 'album_id'
    ];

    public function album()
    {
        return $this->belongsTo(Album::class, 'id', 'album_id');
    }
}
