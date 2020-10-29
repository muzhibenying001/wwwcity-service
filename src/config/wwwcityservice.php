<?php

return [
    'APP_ACCESS_KEY' => env('APP_ACCESS_KEY', ''),
    'APP_ACCESS_SECRET' => env('APP_ACCESS_SECRET', ''),

    'area' => [
        'HOST'=> env('AREA_HOST', '')
    ],

    'user' => [
        'HOST'=> env('USER_HOST', '')
    ],

    'sms' => [
        'HOST'=> env('SMS_HOST', '')
    ],

    'ag' => [
        'HOST'=> env('AG_HOST', '')
    ],

    'finance' => [
        'HOST'=> env('FINANCE_HOST', '')
    ],

    'org' => [
        'HOST'=> env('ORG_HOST', '')
    ],
];
