<?php

namespace App\Http\Livewire;

use App\Package;
use App\Tag;
use Livewire\Component;
use Livewire\WithPagination;

class PackageList extends Component
{
    use WithPagination;

    public $tag = 'all';
    public $search;

    public function render()
    {
        if ($this->tag === 'popular--and--recent') {
            return $this->renderPopularAndRecent();
        }

        return $this->renderPackageList();
    }

    public function renderPopularAndRecent()
    {
        return view('livewire.popular-and-recent-packages', [
            'popularPackages' => Package::popular()->take(6)->with(['author', 'ratings', 'tags'])->withCount('favorites')->get(),
            'recentPackages' => Package::latest()->take(3)->with(['author', 'ratings', 'tags'])->withCount('favorites')->get(),
            'typeTags' => Tag::types()->get(),
            'popularTags' => Tag::popular()->take(10)->get()->sortByDesc('packages_count'),
        ]);
    }

    public function renderPackageList()
    {
        // @todo do we need to eager load author or tags?
        if ($this->search) {
            /*
            // @todo this doesn't actually work. sigh.
            // Maybe we can customize it like this? https://www.algolia.com/doc/api-client/methods/search/
            // ... brain hurts right now.
            $packageQuery = Package::search($this->search)
                ->query(function (Builder $builder) {
                    if ($this->tag !== 'all') {
                        $builder->tagged($this->tag);
                    }
                });
            */

            $packageQuery = $this->tag === 'all' ? Package::query() : Package::tagged($this->tag);
            $packageQuery->where('name', 'like', '%' . $this->search . '%');
        } else {
            $packageQuery = $this->tag === 'all' ? Package::query() : Package::tagged($this->tag);
        }

        return view('livewire.package-list', [
            'packages' => $packageQuery->with(['tags', 'author'])->withCount('favorites')->paginate(6),
            'typeTags' => Tag::types()->get(),
            'popularTags' => Tag::popular()->take(10)->get()->sortByDesc('packages_count'),
        ]);
    }

    public function filterTag($tagSlug)
    {
        $this->tag = $tagSlug;
        $this->goToPage(1);
    }

    public function updatedSearch()
    {
        $this->gotoPage(1);
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

    // temporary--while we have Tailwind pre-1.0
    public function paginationView()
    {
        return 'livewire.partials.tailwind-beta-pagination';
    }
}
