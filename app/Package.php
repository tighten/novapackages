<?php

namespace App;

use App\Screenshot;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Laravel\Scout\Searchable;
use Spatie\Feed\Feedable;
use Spatie\Feed\FeedItem;
use willvincent\Rateable\Rateable;
use willvincent\Rateable\Rating;

class Package extends Model implements Feedable
{
    // @todo add algolia logo next to the search results if it's used in not-api
    use Searchable;
    use Rateable, RatingCountable {
        RatingCountable::averageRating insteadof Rateable;
    }

    protected $guarded = ['id', 'is_disabled'];

    protected $casts = [
        'is_disabled' => 'boolean',
        'packagist_downloads' => 'integer',
        'github_stars' => 'integer',
    ];

    protected $excludeFromSearchIndex = [
        'description',
        'packagist_downloads',
        'github_stars',
        'updated_at',
    ];

    protected $githubStarVsPackagistDownloadsMultiplier = 100;

    public function author()
    {
        return $this->belongsTo(Collaborator::class, 'author_id');
    }

    public function contributors()
    {
        return $this->belongsToMany(Collaborator::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class)->withTimestamps();
    }

    public function screenshots()
    {
        return $this->hasMany(Screenshot::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function ratings()
    {
        return $this->morphMany(Rating::class, 'rateable');
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function scopeTagged($query, $tagSlug)
    {
        $query->whereHas('tags', function ($query) use ($tagSlug) {
            $query->where('slug', $tagSlug);
        });
    }

    public function scopePopular($query)
    {
        return $query->select(
            DB::Raw('packages.*, ((`github_stars` * '.$this->githubStarVsPackagistDownloadsMultiplier.') + `packagist_downloads`) as `popularity`')
        )
            ->orderBy('popularity', 'desc');
    }

    public function toSearchableArray()
    {
        $packageAttributes = $this->toArray();
        // Temporarily truncate to prevent algolia from throwing a size exceeded exception
        $packageAttributes['readme'] = substr($packageAttributes['readme'], 0, 500);
        $packageAttributes['instructions'] = substr($packageAttributes['instructions'], 0, 500);

        Arr::forget($packageAttributes, $this->excludeFromSearchIndex);

        // Add tags so we can filter them @todo not sure if helpful
        // @todo Make sure this is updated when tags are updated
        $packageAttributes['tags'] = $this->tags->toArray();

        return $packageAttributes;
    }

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('notDisabled', function (Builder $builder) {
            $builder->where('is_disabled', false);
        });
    }

    public function getComposerVendorAttribute()
    {
        return Str::before($this->composer_name, '/');
    }

    public function getComposerPackageAttribute()
    {
        return Str::after($this->composer_name, '/');
    }

    public function getAbstractAttribute()
    {
        return $this->attributes['abstract'] ?: abstractify(markdown($this->attributes['readme']));
    }

    /**
     * Output package for RSS feed.
     *
     * @return Spatie\Feed\FeedItem
     */
    public function toFeedItem()
    {
        return FeedItem::create()
            ->id($this->id)
            ->title($this->name)
            ->summary($this->abstract)
            ->updated($this->updated_at)
            ->link(route('packages.show', [$this->composer_vendor, $this->composer_package]))
            ->author($this->author->name);
    }

    public static function getRecentFeedItems()
    {
        return self::latest()->take(20)->get();
    }

    public function syncScreenshots($screenshots)
    {
        $this->screenshots()->update(['package_id' => null]);
        Screenshot::whereIn('id', $screenshots)->update(['package_id' => $this->id]);
    }

    public function authorIsUser()
    {
        return $this->author->user()->exists();
    }

    public function readmeIsHtml()
    {
        return $this->readme_format == 'html';
	}

    public function addReview($ratingId, $reviewContent)
    {
        Review::updateOrCreate(['user_id' => auth()->id(), 'package_id' => $this->id], [
            'rating_id' => $ratingId,
            'content' => $reviewContent,
        ]);
    }

    public function updateReview($reviewContent)
    {
        Review::updateOrCreate(['user_id' => auth()->id(), 'package_id' => $this->id], [
            'content' => $reviewContent,
        ]);
    }
}
