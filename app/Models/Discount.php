<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Discount extends Model
{
    use HasFactory;

    protected $fillable = [
        'amount',
        'type',
        'start_date',
        'end_date',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function discountable(): MorphTo
    {
        return $this->morphTo();
    }
}
