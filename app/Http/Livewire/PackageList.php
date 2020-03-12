<?php

namespace App\Http\Livewire;

use Algolia\AlgoliaSearch\SearchIndex;
use App\CacheKeys;
use App\Package;
use App\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;
use Livewire\WithPagination;

class PackageList extends Component
{
    use WithPagination;

    const POPULAR_TAG = 'popular--and--recent';
    const POPULAR_TAGS_LENGTH = 120;

    public $tag = 'popular--and--recent';
    public $search;
    public $totalPages = 1;
    public $pageSize = 6;

    protected $updatesQueryString = [
        'tag' => ['except' => 'popular--and--recent'],
        'search',
        'page',
    ];

    public function render()
    {
        if ($this->tag === self::POPULAR_TAG) {
            return $this->renderPopularAndRecent();
        }

        return $this->renderPackageList();
    }

    public function renderPopularAndRecent()
    {
        $this->totalPages = 1;

        return view('livewire.popular-and-recent-packages', [
            'popularPackages' => Package::popular()->with(['author', 'ratings'])->paginate(6),
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
            })->paginate($this->pageSize);

            $packages->load(['author', 'ratings']);
        } else {
            $packages = $this->tag === 'all' ? Package::query() : Package::tagged($this->tag);
            $packages = $packages->latest()->with(['author', 'ratings'])->paginate($this->pageSize);
        }

        $this->totalPages = $packages->lastPage();

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

    public function changePageSize($size)
    {
        $this->pageSize = $size;
        $this->goToPage(1);
    }

    /** Livewire Hooks and lifecycle methods */
    public function updatedSearch()
    {
        if ($this->tag === self::POPULAR_TAG) {
            $this->tag = 'all';
        }

        // Prefer null over empty string to remove from query string
        if (! $this->search) {
            $this->search = null;
        }

        $this->gotoPage(1);
    }

    public function updatedTag()
    {
        $this->goToPage(1);
    }

    public function mount(Request $request)
    {
        $this->fill(
            $request->only(['tag', 'search', 'page'])
        );
    }

    /* Fix nextPage/previousPage to disallow overflows */
    public function previousPage()
    {
        if ($this->page > 1) {
            $this->page = $this->page - 1;
        }
    }

    public function nextPage()
    {
        if ($this->page < $this->totalPages) {
            $this->page = $this->page + 1;
        }
    }

    /* Temporary override of WithPagination -- while we have Tailwind pre-1.0 */
    public function paginationView()
    {
        return 'livewire.partials.tailwind-beta-pagination';
    }
}
