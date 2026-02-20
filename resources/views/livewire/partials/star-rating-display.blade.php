@props(['rating' => 0, 'size' => 'text-lg'])

<div class="flex {{ $size }}">
    @for($i = 1; $i <= 5; $i++)
        <span class="{{ $i <= $rating ? 'text-yellow-500' : 'text-gray-300' }}">&#9733;</span>
    @endfor
</div>
