<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class Screenshot extends Model
{
    use HasFactory;

    protected $fillable = [
        'uploader_id',
        'path',
    ];

    protected $appends = ['public_url'];

    public static function booted()
    {
        static::deleted(function ($screenshot) {
            Storage::delete($screenshot->path);
        });
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

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    public function getPublicUrlAttribute()
    {
        return Storage::url($this->path);
    }

    public function hasPackage()
    {
        return $this->package ? true : false;
    }

    #[Scope]
    protected function abandoned($query)
    {
        return $query->doesntHave('package')
            ->where('created_at', '<', Carbon::now()->subHours(24));
    }
}
