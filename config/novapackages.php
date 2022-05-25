<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Latest Version
    |--------------------------------------------------------------------------
    |
    | This value defines the latest major version of Laravel Nova. This value is
    | used when checking if a package is the compatible with the latest version
    | of Nova.
    |
    */

    'nova' => [
        'latest_major_version' => env('NOVA_LATEST_MAJOR_VERSION', '4'),
    ],


    /*
    |--------------------------------------------------------------------------
    | Filtering
    |--------------------------------------------------------------------------
    |
    | The following values are used to filter the information that is displayed
    | on the front end.
    |
    */
    'filtering' => [

        /**
         * Any item in the haystack that doesn't contain a string needs to be placed at the bottom
         * otherwise it will leave the string before the version number.
         */
        'package_name' => [
            'For Nova',
            'For Nova !',
            'For Nova!',
            'Nova !',
            'Nova!',
            'N!',
            'V!',
            'N !',
            'V !',
            '(!)', // Place at bottom
            '!', // Place at bottom
        ]
    ]
];
