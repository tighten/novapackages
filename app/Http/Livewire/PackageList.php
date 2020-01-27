<?php

namespace App\Http\Livewire;

use App\Http\Resources\PackageResource;
use App\Package;
use App\Tag;
use Livewire\Component;

class PackageList extends Component
{
    use Paginates;

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
            'popularPackages' => PackageResource::from(Package::popular()->take(6)->with(['author', 'ratings', 'tags'])->withCount('favorites')->get()),
            'recentPackages' => PackageResource::from(Package::latest()->take(3)->with(['author', 'ratings', 'tags'])->withCount('favorites')->get()),
            'typeTags' => Tag::types()->get(),
            // 'popularTags' => Tag::popular()->take(10)->get()->sortByDesc('packages_count'),
        ]);
    }

    public function renderPackageList()
    {
        $packageQuery = $this->tag === 'all' ? Package::query() : Package::tagged($this->tag);

        return view('livewire.package-list', [
            'packages' => PackageResource::from($this->paginate($this->addSearch($packageQuery))->get()),
            'typeTags' => Tag::types()->get(),
            // 'popularTags' => Tag::popular()->take(10)->get()->sortByDesc('packages_count'),
        ]);
    }

    public function addSearch($query)
    {
        if ($this->search) {
            // return $query->search($this->search);
            return $query->where('name', 'like', '%' . $this->search . '%');
        }

        return $query;
    }

    public function filterTag($tagSlug)
    {
        $this->tag = $tagSlug;
        $this->page = 1;
    }

    public function mount()
    {
        return;

        // @todo later when we are handling query string updates
        // in updated version of Livewire
        if (request()->has('query')) {
            // @todo initial scope
        }
    }
}
