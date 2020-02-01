<?php

namespace App\Http\Livewire;

use Algolia\AlgoliaSearch\SearchIndex;
use App\CacheKeys;
use App\Package;
use App\Tag;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;
use Livewire\WithPagination;

class PackageList extends Component
{
    use WithPagination;

    const POPULAR_TAG = 'popular--and--recent';
    const POPULAR_TAGS_LENGTH = 120;

    public $tag = 'all';
    public $search;

    public function render()
    {
        if ($this->tag === self::POPULAR_TAG) {
            return $this->renderPopularAndRecent();
        }

        return $this->renderPackageList();
    }

    public function renderPopularAndRecent()
    {
        return view('livewire.popular-and-recent-packages', [
            'popularPackages' => Package::popular()->take(6)->with(['author', 'ratings'])->get(),
            'recentPackages' => Package::latest()->take(3)->with(['author', 'ratings'])->get(),
            'typeTags' => Tag::types()->get(),
            'popularTags' => $this->topTenPopularTags(),
        ]);
    }

    public function renderPackageList()
    {
        if ($this->search) {
            $packages = Package::search($this->search, function (SearchIndex $algolia, string $query, array $options) {
                if ($this->tag !== 'all') {
                    $options['tagFilters'] = [$this->tag];
                }

                return $algolia->search($query, $options);
            })->paginate(6);

            $packages->load(['author', 'ratings']);
        } else {
            $packages = $this->tag === 'all' ? Package::query() : Package::tagged($this->tag);
            $packages = $packages->latest()->with(['author', 'ratings'])->paginate(6);
        }

        return view('livewire.package-list', [
            'packages' => $packages->onEachSide(3),
            'typeTags' => Tag::types()->get(),
            'popularTags' => $this->topTenPopularTags(),
        ]);
    }

    private function topTenPopularTags()
    {
        return Cache::remember(
            CacheKeys::popularTags(),
            self::POPULAR_TAGS_LENGTH,
            function () {
                return Tag::popular()->take(10)->get()->sortByDesc('packages_count');
            }
        );
    }

    public function filterTag($tagSlug)
    {
        $this->tag = $tagSlug;
        $this->goToPage(1);
    }

    /** Livewire Hooks and lifecycle methods */
    public function updatedSearch()
    {
        if ($this->tag === self::POPULAR_TAG) {
            $this->tag = 'all';
        }

        $this->gotoPage(1);
    }

    public function updatedTag()
    {
        $this->goToPage(1);
    }

    public function mount()
    {
        return;

        // @todo later when we are handling query string updates
        // in updated version of Livewire
        if (request()->has('query')) {
            // initial scope based on query
        }
    }

    /* Temporary override of WithPagination -- wwhile we have Tailwind pre-1.0 */
    public function paginationView()
    {
        return 'livewire.partials.tailwind-beta-pagination';
    }
}
