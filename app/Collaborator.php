<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Collaborator extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $appends = ['name_with_username'];

    protected function casts(): array
    {
        return [
            'user_id' => 'integer'
        ];
    }

    public function allAuthoredPackages(): HasMany
    {
        return $this->hasMany(Package::class, 'author_id')->withoutGlobalScope('notDisabled');
    }

    public function authoredPackages(): HasMany
    {
        return $this->hasMany(Package::class, 'author_id');
    }

    public function contributedPackages(): BelongsToMany
    {
        return $this->belongsToMany(Package::class);
    }

    public function submittedPackages(): HasMany
    {
        return $this->hasMany(Package::class, 'submitter_id', 'user_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getnameWithUsernameAttribute()
    {
        if (! $this->github_username) {
            return $this->name;
        }

        return "{$this->name} ({$this->github_username})";
    }

    public function getRouteKeyName()
    {
        return 'github_username';
    }

    public function scopeInRequest($query, $request)
    {
        return self::whereIn('id', array_merge([$request->input('author_id')], $request->input('contributors', [])));
    }
}
