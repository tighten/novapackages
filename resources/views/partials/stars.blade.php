@php
for ($i = 1; $i <= 5; $i++) {
    if ($i <= $stars) {
        $starPercentages[$i] = 100;
    } elseif ($i - $stars > 0 && $i - $stars < 1) {
        $starPercentages[$i] = ($i - $stars) * 100;
    } else {
        $starPercentages[$i] = 0;
    }
}
@endphp

<div class="vue-star-rating">
    <div class="vue-star-rating">
        @foreach ($starPercentages as $value)
            @php
                $id = Str::random(6);
            @endphp
            <span class="vue-star-rating-star inline" style="margin-right: 0px;"><svg height="20" width="20" viewBox="0 0 20 20" class="vue-star-rating-star inline" step="10"><linearGradient id="{{ $id }}" x1="0" x2="100%" y1="0" y2="0"><stop offset="{{ $value }}%" stop-color="#ffd055"></stop> <stop offset="{{ $value }}%" stop-color="#d8d8d8"></stop></linearGradient> <filter id="{{ $id }}" height="130%" width="130%" filterUnits="userSpaceOnUse"><feGaussianBlur stdDeviation="0" result="coloredBlur"></feGaussianBlur> <feMerge ><feMergeNode in="coloredBlur"></feMergeNode> <feMergeNode in="SourceGraphic"></feMergeNode></feMerge></filter> <polygon points="9.090909090909092,1.0101010101010102,3.0303030303030303,20,18.181818181818183,7.878787878787879,0,7.878787878787879,15.15151515151515,20" fill="url(#{{ $id }})" stroke="#fff" filter="url(#yxba3h)"></polygon> <polygon points="9.090909090909092,1.0101010101010102,3.0303030303030303,20,18.181818181818183,7.878787878787879,0,7.878787878787879,15.15151515151515,20" fill="url(#{{ $id }})" stroke="#999" stroke-width="0" stroke-linejoin="miter"></polygon> <polygon points="9.090909090909092,1.0101010101010102,3.0303030303030303,20,18.181818181818183,7.878787878787879,0,7.878787878787879,15.15151515151515,20" fill="url(#{{ $id }})"></polygon></svg></span>
        @endforeach
    </div>
</div>
