<?php

namespace App\Http\Livewire;

trait Paginates
{
    public $page = 1;
    public $perPage = 6;
    public $packageCount;

    public function paginate($query)
    {
        $this->packageCount = $query->count();

        return $query->skip($this->perPage * ($this->page - 1))->take($this->perPage);
    }

    public function getPageCountProperty()
    {
        return ceil($this->packageCount / $this->perPage);
    }

    public function nextPage()
    {
        if ($this->page < $this->pageCount) {
            $this->page++;
        }
    }

    public function prevPage()
    {
        if ($this->page > 1) {
            $this->page--;
        }
    }
}
