<?php

namespace App;

class Colors
{
    public static $index = 0;

    public static $colors = [
        '#6574cd', // Blue
        '#41ac9c', // Teal
        '#e49334', // Orange
        '#56ad34', // Green
        '#c34949', // Red
        '#a72b9d', // Purple
        '#d2c823', // Yellow
    ];

    public static function nextColor()
    {
        if (self::$index >= count(self::$colors)) {
            self::$index = 0;
        }

        return self::$colors[self::$index++];
    }
}
