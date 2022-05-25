<?php

namespace App\Helpers;

use Illuminate\Support\Arr;
use Illuminate\Support\Traits\Macroable;

class TrimPackageName
{
    use Macroable;

    /**
     * @param string $packageName
     * @return string
     */
    public static function trim(string $packageName): string
    {
        // The haystack used to check if the string contains any of the invalid substrings.
        $versionHaystack = [];

        // Create the version haystack for each version of Laravel Nova.
        $v = 1;
        while ($v <= config('novapackages.nova.latest_major_version')) {
            foreach (config('novapackages.filtering.package_name') as $subject) {
                // Replace ! with the version number.
                $versionHaystack[] = str_replace('!', $v, $subject);
            }

            $v++;
        }

        // Filter the string to only contain strings that contain the version number case insensitive.
        $filteredVersions = str_ireplace($versionHaystack, '', $packageName);

        // Filter extra spaces created by the removing elements from the version haystack.
        $filtered = preg_replace('!\s+!', ' ', $filteredVersions);

        return $filtered;
    }
}
