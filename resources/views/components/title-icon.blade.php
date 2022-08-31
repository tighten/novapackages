@props([
    'image' => null,
    'size',
    'title',
])

<div
    @class([
        'h-8 w-8 mr-2 text-md' => $size === 'small',
        'h-10 w-10 mr-4 text-md' => $size === 'medium',
        'h-16 w-16 mr-4 text-lg' => $size === 'large',
        'h-24 w-24 mr-6 text-xl' => $size === 'xlarge',
    ])
>
    @if ($image)
        <img alt="{{ $title }}" src="{{ $image }}" class="rounded-full inline-block bg-gray-600"/>
    @else
        <div
            @class([
                'rounded-full inline-block bg-slate-500 text-white text-center leading-none flex flex-row items-center justify-center',
                'h-8 w-8 mr-2 text-md' => $size === 'small',
                'h-10 w-10 mr-4 text-md' => $size === 'medium',
                'h-16 w-16 mr-4 text-lg' => $size === 'large',
                'h-24 w-24 mr-6 text-xl' => $size === 'xlarge',
            ])
        >
            {{ str($title)->substr(0, 1)->upper() }}
        </div>
    @endif
</div>
