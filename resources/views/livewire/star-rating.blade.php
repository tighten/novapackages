<div class="flex">
    @foreach (range(1, 5) as $point)
        <svg
            @if (! $readOnly)
                wire:click="rate({{ $point }})"
            @endif
            @class([
                'inline-block fill-current h-5 w-5',
                'cursor-pointer' => ! $readOnly,
                'text-yellow-500' => $rating >= $point,
                'text-gray-400' => $rating <= $point,
            ])
            xmlns="http://www.w3.org/2000/svg"
            xmlns:xlink="http://www.w3.org/1999/xlink"
            viewBox="0 0 20 20"
        >
            <g stroke="none" stroke-width="1" fill-rule="evenodd">
                <g>
                    <polygon id="Star-3" points="10 15 4.12214748 18.0901699 5.24471742 11.545085 0.489434837 6.90983006 7.06107374 5.95491503 10 0 12.9389263 5.95491503 19.5105652 6.90983006 14.7552826 11.545085 15.8778525 18.0901699"></polygon>
                </g>
            </g>
        </svg>
    @endforeach
</div>
