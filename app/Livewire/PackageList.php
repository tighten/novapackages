<?php

namespace App\Livewire;

use App\CacheKeys;
use App\Package;
use App\Tag;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Cookie;
use Livewire\Component;
use Livewire\WithPagination;
use Typesense\Documents;

class PackageList extends Component
{
    use WithPagination;

    const POPULAR_TAG = 'popular--and--recent';
    const POPULAR_TAGS_LENGTH = 120;

    public $tag = 'popular--and--recent';
    public $search;
    public $pageSize = 6;

    protected $queryString = [
        'tag' => ['except' => 'popular--and--recent'],
        'search' => ['except' => ''],
        'page' => ['except' => 1],
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
            $packages = Package::search($this->search, function (Documents $documents, string $query) {
                $searchParams = [
                    'q' => $query,
                    'query_by' => config('scout.typesense.model-settings.' . Package::class . '.search-parameters.query_by'),
                ];

                if (! in_array($this->tag, ['all', 'popular', 'nova_current'])) {
                    $searchParams['filter_by'] = '_tags: [' . $this->tag . ']';
                }

                return $documents->search($searchParams);
            })->query(function (Builder $builder) {
                // Ensure search results use the same query scopes as non-filtered results
                return $builder->filter($this->tag);
            })->paginate($this->pageSize);

            $packages->load(['author', 'ratings']);
        } else {
            if (in_array($this->tag, ['all', 'popular', 'nova_current'])) {
                $packages = Package::filter($this->tag);
            } else {
                $packages = Package::tagged($this->tag);
            }

            $packages = $packages->latest()->with(['author', 'ratings'])->paginate($this->pageSize);
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

    public function changePageSize($size)
    {
        $this->pageSize = $size;
        $this->goToPage(1);

        Cookie::queue('pageSize', $size, 2880);
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
        $this->pageSize = Cookie::get('pageSize', $this->pageSize);
    }

    /* Temporary override of WithPagination -- while we have Tailwind pre-1.0 */
    public function paginationView()
    {
        return 'livewire.partials.tailwind-beta-pagination';
    }
}
