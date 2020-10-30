<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use willvincent\Rateable\Rating;

class Review extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $with = ['rating:id,rating'];

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function rating()
    {
        return $this->belongsTo(Rating::class);
    }
}
