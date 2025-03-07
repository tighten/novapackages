<?php

use Monolog\Processor\PsrLogMessageProcessor;
use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\SyslogUdpHandler;

return [

    'channels' => [
        'bugsnag' => [
            'driver' => 'bugsnag',
        ],

        'flare' => [
            'driver' => 'flare',
        ]
    ],

];
