@props(['stars', 'count', 'totalCount'])

@php
    $percent = $totalCount == 0 ? 0 : round($count / $totalCount * 100);
@endphp

<div class="flex my-1">
    <div class="w-1/3 text-xs pr-1 text-gray-500 text-right">{{ str_repeat("\u{2605}", $stars) }}</div>
    <div class="w-2/3 w-full bg-gray-200 h-2 mt-1">
        <div class="bg-yellow-500 h-2" style="width: {{ $percent }}%"></div>
    </div>
</div>
