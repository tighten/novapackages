@php
$iconSizes = [
    'small' => 'h-8 w-8 mr-2 text-md',
    'medium' => 'h-10 w-10 mr-4 text-md',
    'large' => 'h-16 w-16 mr-4 text-lg',
    'xlarge' => 'h-24 w-24 mr-6 text-xl',
];
$cssClass = $iconSizes[$size ?? 'small'];
@endphp
<div class="{{ $cssClass }}">
    <div class="{{ $cssClass }} rounded-full inline-block text-white text-center leading-none flex flex-row items-center justify-center" style="background-color: {{ $color ?? '#606f7b' }}">
        {{ strtoupper(substr(str_replace("Nova ", "", $title), 0, 1)) }}
    </div>
</div>
