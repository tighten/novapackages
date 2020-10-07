<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class Screenshot extends Model
{
    protected $fillable = [
        'uploader_id',
        'path',
    ];

    protected $appends = ['public_url'];

    public function uploader()
    {
        return $this->belongsTo(User::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function scopeAbandoned($query)
    {
        return $query->doesntHave('package')
            ->where('created_at', '<', Carbon::now()->subHours(24));
    }

    public static function booted()
    {
        static::deleted(function ($screenshot) {
            Storage::delete($screenshot->path);
        });
    }

    public function getPublicUrlAttribute()
    {
        return Storage::url($this->path);
    }

    public static function forRequest($screenshotIds)
    {
        if (! is_array($screenshotIds)) {
            return [];
        }

        return self::whereIn('id', $screenshotIds)->get()->map(function ($screenshot) {
            return $screenshot->toArray();
        });
    }

    public function hasPackage()
    {
        return $this->package ? true : false;
    }
}
