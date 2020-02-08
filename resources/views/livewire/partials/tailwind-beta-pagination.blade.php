{{-- https://gist.github.com/mazedlx/86512703b1dbcb987b2815c31e5173a3 --}}
@if ($paginator->hasPages())
    <div class="flex w-full items-center">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <span class="bg-white rounded-l rounded-sm border border-brand-light px-3 py-2 cursor-not-allowed no-underline text-brand-light">&laquo;</span>
        @else
            <a
                class="bg-white rounded-l rounded-sm border-t border-b border-l border-brand-light cursor-pointer px-3 py-2 text-brand-dark hover:bg-brand hover:text-white no-underline"
                wire:click="previousPage"
                rel="prev"
            >
                &laquo;
            </a>
        @endif

        {{-- Pagination Elements --}}
        @foreach ($elements as $element)
            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
                <span class="bg-white border-t border-b border-l border-brand-light px-3 py-2 cursor-not-allowed no-underline">{{ $element }}</span>
            @endif

            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="border-t border-b border-l border-brand-light px-3 py-2 bg-brand-light hover:bg-brand text-white no-underline">{{ $page }}</span>
                    @else
                        <a class="bg-white border-t border-b border-l border-brand-light cursor-pointer px-3 py-2 hover:bg-brand hover:text-white text-brand-dark no-underline" wire:click="gotoPage({{ $page }})">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a class="bg-white rounded-r rounded-sm border border-brand-light cursor-pointer px-3 py-2 hover:bg-brand hover:text-white text-brand-dark no-underline" wire:click="nextPage" rel="next">&raquo;</a>
        @else
            <span class="bg-white rounded-r rounded-sm border border-brand-light px-3 py-2 text-brand-light no-underline cursor-not-allowed">&raquo;</span>
        @endif
    </div>
@endif
