<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrackStockUpdate extends Model
{
    use HasFactory;

    protected $fillable = [
        'previous_stock',
        'current_stock',
        'note',
        'product_id'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
