<?php

namespace App\Models;

use App\Traits\HasImage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;

class Banner extends Model implements HasMedia
{
    use HasFactory, HasImage, InteractsWithMedia;

    protected $fillable = [
        'title',
        'subtitle',
        'description',
        'button_url',
        'button_text',
    ];

    protected $hidden = [
        'media',
        'created_at',
        'updated_at',
    ];

    // Accessor for image_url
    protected $appends = ['image_url'];
}
