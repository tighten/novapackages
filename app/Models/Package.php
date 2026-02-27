<?php

namespace App\Models;

use App\OpenGraphImage;
use App\RatingCountable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Laravel\Scout\Searchable;
use Spatie\Feed\Feedable;
use Spatie\Feed\FeedItem;
use willvincent\Rateable\Rateable;
use willvincent\Rateable\Rating;

class Package extends Model implements Feedable
{
    use HasFactory;
    use Rateable, RatingCountable {
        RatingCountable::averageRating insteadof Rateable;
    }
    use Searchable;

    protected $guarded = ['id', 'is_disabled'];

    protected $excludeFromSearchIndex = [
        'description',
        'packagist_downloads',
        'github_stars',
        'updated_at',
    ];

    protected $githubStarVsPackagistDownloadsMultiplier = 100;

    public static function getRecentFeedItems()
    {
        return self::latest()->take(20)->get();
    }

    protected static function booted()
    {
        static::addGlobalScope('notDisabled', function (Builder $builder) {
            $builder->where('is_disabled', false);
        });
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(Collaborator::class, 'author_id');
    }

    public function contributors(): BelongsToMany
    {
        return $this->belongsToMany(Collaborator::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class)->withTimestamps();
    }

    public function screenshots(): HasMany
    {
        return $this->hasMany(Screenshot::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function ratings(): MorphMany
    {
        return $this->morphMany(Rating::class, 'rateable');
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    public function toSearchableArray()
    {
        $packageAttributes = $this->toArray();

        $packageAttributes['created_at'] = $this->created_at->timestamp;

        Arr::forget($packageAttributes, $this->excludeFromSearchIndex);

        // Add tags so we can filter them
        // @todo Make sure this is updated when tags are updated
        $packageAttributes['_tags'] = $this->tags->pluck('slug')->toArray();

        return [
            'id' => (string) $packageAttributes['id'],
            'name' => (string) ($packageAttributes['name'] ?? ''),
            'url' => (string) ($packageAttributes['url'] ?? ''),
            'instructions' => (string) ($packageAttributes['instructions'] ?? ''),
            'composer_name' => (string) ($packageAttributes['composer_name'] ?? ''),
            'repo_url' => (string) ($packageAttributes['repo_url'] ?? ''),
            'readme' => (string) ($packageAttributes['readme'] ?? ''),
            'abstract' => (string) ($packageAttributes['abstract'] ?? ''),
            '_tags' => ($packageAttributes['_tags'] ?? []),
            'created_at' => $packageAttributes['created_at'],
        ];
    }

    public function getDisplayNameAttribute()
    {
        // The haystack used to check if the string contains any of the invalid substrings.
        $toRemove = [];

        // Create the version haystack for each version of Laravel Nova.
        foreach (config('novapackages.filtering.package_name') as $subject) {
            $v = config('novapackages.nova.latest_major_version');
            while ($v >= 1) {
                // Replace ! with the version number.
                $toRemove[] = Str::replace('!', $v, $subject);

                $v--;
            }
        }

        // Remove matches, trim the string and remove double spaces.
        return Str::of($this->name)->remove($toRemove, false)->trim()->replaceMatches('!\s+!', ' ');
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

    public function getOgImageNameAttribute()
    {
        return OpenGraphImage::makeFileName($this->id, $this->name);
    }

    public function getOgImagePublicUrlAttribute()
    {
        return Storage::url(config('opengraph.image_directory_name') . "/{$this->og_image_name}");
    }

    /**
     * Output package for RSS feed.
     */
    public function toFeedItem(): FeedItem
    {
        return FeedItem::create()
            ->id($this->id)
            ->title($this->name)
            ->summary($this->abstract)
            ->updated($this->updated_at)
            ->link(route('packages.show', [$this->composer_vendor, $this->composer_package]))
            ->authorName($this->author->name);
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

    public function updateAvailabilityFromNewUrl()
    {
        if (is_null($this->marked_as_unavailable_at)) {
            return;
        }

        if (array_key_exists('url', $this->getChanges())) {
            $this->marked_as_unavailable_at = null;
            $this->is_disabled = false;
            $this->save();
        }
    }

    public function getIsPossiblyAbandonedAttribute(): bool
    {
        return $this->packagist_downloads === 0
            && $this->created_at->diffInDays(now()) > 30;
    }

    protected function casts(): array
    {
        return [
            'is_disabled' => 'boolean',
            'is_abandoned' => 'boolean',
            'packagist_downloads' => 'integer',
            'github_stars' => 'integer',
        ];
    }

    #[Scope]
    protected function filter($query, string $tag)
    {
        switch ($tag) {
            case 'popular':
                return $query->popular();
            case 'nova_current':
                return $query->novaCurrent();
            default:
                return $query;
        }
    }

    #[Scope]
    protected function tagged($query, $tagSlug)
    {
        $query->whereHas('tags', function ($query) use ($tagSlug) {
            $query->where('slug', $tagSlug);
        });
    }

    #[Scope]
    protected function popular($query)
    {
        return $query->select(
            DB::raw('packages.*, ((`github_stars` * ' . $this->githubStarVsPackagistDownloadsMultiplier . ') + `packagist_downloads`) as `popularity`')
        )
            ->orderBy('popularity', 'desc');
    }

    #[Scope]
    protected function novaCurrent($query)
    {
        return $query->where('nova_version', config('novapackages.nova.latest_major_version'));
    }
}
