<?php

namespace App;

use App\Favorite;
use App\Jobs\UserRatePackage;
use App\Jobs\UserReviewPackage;
use App\Package;
use App\Review;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use Notifiable, HasApiTokens;

    const USER_ROLE = 0;

    const ADMIN_ROLE = 1;

    protected $roles = [
        self::USER_ROLE => 'user',
        self::ADMIN_ROLE => 'admin',
    ];

    protected $casts = [
        'role' => 'integer',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'avatar', 'github_username',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public static function boot()
    {
        parent::boot();
        self::updated(function ($user) {
            $user->updateCollaboratorNames();
        });
    }

    public function collaborators()
    {
        return $this->hasMany(Collaborator::class);
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function favoritePackages()
    {
        return $this->favorites()->has('package')->with('package')->get()->map->package;
    }

    public function getRoleNameAttribute()
    {
        return $this->roles[$this->attributes['role']];
    }

    public function isAdmin()
    {
        return $this->role === self::ADMIN_ROLE;
    }

    public function ratePackage($packageId, $stars)
    {
        if (is_object($packageId)) {
            $packageId = $packageId->id;
        }

        dispatch(new UserRatePackage($this->id, $packageId, $stars));
    }

    public function reviewPackage($packageId, $reviewContent)
    {
        if (is_object($packageId)) {
            $packageId = $packageId->id;
        }

        $this->submitReview($packageId, $reviewContent);
    }

    public function favoritePackage($packageId)
    {
        Favorite::updateOrCreate([
            'package_id' => Package::findOrFail($packageId)->id,
            'user_id' => $this->id,
        ]);
    }

    public function unfavoritePackage($packageId)
    {
        $this->favorites()->where('package_id', $packageId)->delete();
    }

    public static function forSocialiteUser($socialiteUser)
    {
        return self::where('github_username', $socialiteUser->getNickname())
            ->orWhere('email', $socialiteUser->getEmail())
            ->first();
    }

    public function updateCollaboratorNames()
    {
        $this->collaborators
            ->filter(function ($collaborator) {
                return $collaborator->github_username === $this->github_username;
            })
            ->each(function ($collaborator) {
                $collaborator->update(['name' => $this->name]);
            });
    }
}
