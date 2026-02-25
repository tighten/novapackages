<?php

namespace App\Livewire;

use App\CacheKeys;
use App\Collaborator;
use App\Events\PackageRated;
use App\Exceptions\PackagistException;
use App\Exceptions\SelfAuthoredRatingException;
use App\Favorite;
use App\Http\Remotes\Packagist;
use App\Jobs\SyncPackageRepositoryData;
use App\Package;
use App\ReadmeFormatter;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class PackageShow extends Component
{
    public Package $package;
    public bool $creatingReview = false;

    public bool $isFavorite = false;
    public int $favoritesCount = 0;
    public bool $rated = false;
    public int $currentUserRating = 0;
    public bool $refreshRequested = false;
    public bool $repositoryRefreshRequested = false;

    public function mount(Package $package, bool $creatingReview = false)
    {
        $this->package = $package;
        $this->creatingReview = $creatingReview;

        if (auth()->check()) {
            $this->isFavorite = auth()->user()->favorites()->where('package_id', $package->id)->exists();
            $this->currentUserRating = (int) $this->getUserRating();
        }

        $this->favoritesCount = Favorite::where('package_id', $package->id)->count();
    }

    public function setRating(int $stars)
    {
        try {
            auth()->user()->ratePackage($this->package->id, $stars);
        } catch (SelfAuthoredRatingException $e) {
            $this->dispatch('toast', message: 'A package cannot be rated by its author.', type: 'error');

            return;
        }

        event(new PackageRated($this->package->id));

        $this->currentUserRating = $stars;
        $this->rated = true;
        $this->dispatch('toast', message: 'Rating saved.');

        // Clear rating caches
        Cache::forget(CacheKeys::ratingsCounts(Package::class, $this->package->id));
        Cache::forget(CacheKeys::averageRating(Package::class, $this->package->id));
        Cache::forget(CacheKeys::userPackageRating(auth()->id(), $this->package->id));
    }

    public function toggleFavorite()
    {
        if ($this->isFavorite) {
            auth()->user()->unfavoritePackage($this->package->id);
            $this->isFavorite = false;
            $this->favoritesCount--;
            $this->dispatch('toast', message: 'Removed from favorites.');
        } else {
            auth()->user()->favoritePackage($this->package->id);
            $this->isFavorite = true;
            $this->favoritesCount++;
            $this->dispatch('toast', message: 'Added to favorites.');
        }
    }

    public function requestPackagistRefresh()
    {
        Cache::forget(CacheKeys::packagistData($this->package->composer_name));
        $this->refreshRequested = true;
        $this->dispatch('toast', message: 'Packagist data refresh requested.');
    }

    public function requestRepositoryRefresh()
    {
        dispatch(new SyncPackageRepositoryData($this->package));
        $this->repositoryRefreshRequested = true;
        $this->dispatch('toast', message: 'Repository refresh queued.');
    }

    public function toggleDisabled()
    {
        $this->package->is_disabled = ! $this->package->is_disabled;
        $this->package->save();
        $this->dispatch('toast', message: $this->package->is_disabled ? 'Package disabled.' : 'Package enabled.');
    }

    public function render()
    {
        $this->package->load(['author', 'contributors', 'tags', 'screenshots', 'reviews.user', 'reviews.rating', 'ratings']);

        $packagist = $this->getPackagistData();

        $currentUserOwns = auth()->check() && auth()->user()->can('update', $this->package);
        $currentUserReview = auth()->check()
            ? $this->package->reviews->where('user_id', auth()->id())
            : collect();

        $isSelfAuthored = auth()->check() && $this->package->author_id === (int) optional(
            Collaborator::where('user_id', auth()->id())->first()
        )->id;

        $isSelfContributed = auth()->check() && $this->package->contributors()->pluck('user_id')->contains(auth()->id());

        $ratingCounts = [
            ['number' => 5, 'count' => $this->package->countStarRatings(5)],
            ['number' => 4, 'count' => $this->package->countStarRatings(4)],
            ['number' => 3, 'count' => $this->package->countStarRatings(3)],
            ['number' => 2, 'count' => $this->package->countStarRatings(2)],
            ['number' => 1, 'count' => $this->package->countStarRatings(1)],
        ];

        $totalRatings = collect($ratingCounts)->sum('count');

        return view('livewire.package-show', [
            'packagistData' => $packagist['packagistData'],
            'composerLatest' => $packagist['composerLatest'],
            'novaVersion' => $packagist['novaVersion'],
            'readme' => $this->getFormattedReadme(),
            'instructions' => $this->getFormattedInstructions(),
            'possiblyAbandoned' => $this->isPossiblyAbandoned($packagist['composerLatest'], $packagist['packagistData']),
            'currentUserOwns' => $currentUserOwns,
            'currentUserReview' => $currentUserReview,
            'isSelfAuthored' => $isSelfAuthored,
            'isSelfContributed' => $isSelfContributed,
            'ratingCounts' => $ratingCounts,
            'totalRatings' => $totalRatings,
            'averageRating' => number_format($this->package->averageRating(), 2, '.', ''),
        ]);
    }

    protected function getUserRating()
    {
        if (auth()->guest()) {
            return 0;
        }

        $key = CacheKeys::userPackageRating(auth()->id(), $this->package->id);

        return Cache::remember($key, 5, function () {
            return (int) $this->package->user_average_rating;
        });
    }

    protected function getPackagistData()
    {
        try {
            $packagistData = Packagist::make($this->package->composer_name)->data();

            if (! is_null($packagistData)) {
                $composerLatest = collect($packagistData['package']['versions'] ?? [])
                    ->reject(fn ($version) => str_contains($version['version'], 'dev'))
                    ->first();

                $novaVersion = $composerLatest['require']['laravel/nova'] ?? null;
            }
        } catch (PackagistException $e) {
            $packagistData = null;
        }

        return [
            'packagistData' => $packagistData ?? null,
            'composerLatest' => $composerLatest ?? null,
            'novaVersion' => $novaVersion ?? null,
        ];
    }

    protected function getFormattedReadme()
    {
        if (! $this->package->readme) {
            return '<p>Readme not found. Refer to the project website: <a href="' . e($this->package->url) . '">' . e($this->package->url) . '</a></p>';
        }

        return (new ReadmeFormatter($this->package))->format($this->package->readme);
    }

    protected function getFormattedInstructions()
    {
        if (! $this->package->instructions) {
            return null;
        }

        return markdown($this->package->instructions);
    }

    protected function isPossiblyAbandoned($composerLatest, $packagistData)
    {
        return Arr::get($packagistData ?? [], 'package.abandoned', false) ||
            ($this->package->created_at->diffInDays(now()) > 16 && ! $composerLatest);
    }
}
