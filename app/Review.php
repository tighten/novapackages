<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use willvincent\Rateable\Rating;

class Review extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $with = ['rating:id,rating'];

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function rating(): BelongsTo
    {
        return $this->belongsTo(Rating::class);
    }
}
