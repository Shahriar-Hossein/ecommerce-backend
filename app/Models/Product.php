<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;



class Product extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'title',
        'images',
        'stock_quantity',
        'price',
        'currency',
        'trending',
        'notify_users',
        'description',
        'category_id',
    ];

    protected $casts = [
        'images' => 'array',
        'notify_users' => 'array',
    ];

    protected $hidden = [
        'images',
        'media',
        'created_at',
        'updated_at',
        'notify_users',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function trackStockUpdates(): HasMany
    {
        return $this->hasMany(TrackStockUpdate::class);
    }

    protected $appends = ['image_urls'];

    public function getImageUrlsAttribute(): array
    {
        $image_urls = [];
        foreach ($this->getMedia() as $media) {
            $image_urls[] = $media->getUrl();
        }
        return $image_urls;
    }

    /**
     * @throws FileIsTooBig
     * @throws FileDoesNotExist
     */
    public function uploadImages($images = [] ): void
    {
        $this->clearMediaCollection();
        foreach($images as $image){
            $this->addMedia($image)->toMediaCollection();
        }
    }

    public function trackStockUpdate( $note ): void
    {
        $this->trackStockUpdates()->create([
            'current_stock' => $this->stock_quantity,
            'previous_stock' => $this->getOriginal('stock_quantity'),
            'note' => $note,
        ]);
    }

    // relationships
    public function discounts()
    {
        return $this->morphMany(Discount::class, 'discountable');
    }
}
