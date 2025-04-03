<?php

namespace App\Models;

use App\Traits\HasImage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Category extends Model implements HasMedia
{
    use HasFactory, HasImage, InteractsWithMedia;

    protected $fillable = [
        'title',
    ];

    protected $hidden = [
        'media',
        'created_at',
        'updated_at',
    ];

    protected $appends = ['image_url'];

    // relationships
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function discounts(): MorphMany
    {
        return $this->morphMany(Discount::class, 'discountable');
    }
}
