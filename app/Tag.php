<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Tag extends Model
{
    use HasFactory;

    const PROJECT_TYPES = [
        'action',
        'base resource',
        'card',
        'field',
        'filter',
        'lens',
        'partition',
        'resource',
        'resource tool',
        'theme',
        'tool',
        'trend',
        'value',
    ];

    protected $guarded = ['id'];

    public function packages()
    {
        return $this->belongsToMany(Package::class)->withTimestamps();
    }

    public function projectTypeSlugs()
    {
        return collect(self::PROJECT_TYPES)->map(function ($name) {
            return Str::slug($name);
        })->toArray();
    }

    public function scopePopular($query)
    {
        return $query->nonTypes()
            ->whereHas('packages')
            ->whereNotIn('name', ['Laravel', 'Nova', 'Laravel Nova'])
            ->withCount('packages')
            ->orderByDesc('packages_count');
    }

    public function scopeTypes($query)
    {
        return $query->whereIn('slug', $this->projectTypeSlugs());
    }

    public function scopeNonTypes($query)
    {
        return $query->whereNotIn('slug', $this->projectTypeSlugs());
    }

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = Str::lower($value);
    }

    public function url()
    {
        return url('?tag=' . $this->slug);
    }
}
