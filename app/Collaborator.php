<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Collaborator extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = ['user_id' => 'integer'];

    public function allAuthoredPackages()
    {
        return $this->hasMany(Package::class, 'author_id')->withoutGlobalScope('notDisabled');
    }

    public function authoredPackages()
    {
        return $this->hasMany(Package::class, 'author_id');
    }

    public function contributedPackages()
    {
        return $this->belongsToMany(Package::class);
    }

    public function submittedPackages()
    {
        return $this->hasMany(Package::class, 'submitter_id', 'user_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
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
