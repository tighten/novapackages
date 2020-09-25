<?php

use Illuminate\Support\Arr;

if (! function_exists('markdown')) {
    function markdown($text)
    {
        return '<div class="markdown">'.(new Parsedown)->text($text).'</div>';
    }
}

if (! function_exists('abstractify')) {
    function abstractify($text)
    {
        $text = strip_tags($text);

        return strlen($text) > 190 ? substr($text, 0, 190).'...' : $text;
    }
}

if (! function_exists('translate_github_emoji')) {
    function translate_github_emoji($key)
    {
        return Arr::get([
            '+1' => '👍',
            '-1' => '👎',
            'laugh' => '😁',
            'hooray' => '🎉',
            'confused' => '😕',
            'heart' => '❤️',
        ], $key, '⁉️');
    }
}
