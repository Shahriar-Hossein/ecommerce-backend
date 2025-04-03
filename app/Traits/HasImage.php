<?php

namespace App\Traits;

use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;
use Illuminate\Http\Request;

trait HasImage
{
    public function getImageUrlAttribute(): string
    {
        return $this->getFirstMediaUrl();
    }

    /**
     * @throws FileIsTooBig
     * @throws FileDoesNotExist
     */
    public function uploadImage(Request $request): void
    {
        if($request->hasFile('image')) {
            $this->clearMediaCollection();
            $this->addMediaFromRequest('image')->toMediaCollection();
        }
    }
}
