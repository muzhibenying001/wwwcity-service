<?php

return [
    'APP_ACCESS_KEY' => env('APP_ACCESS_KEY', ''),
    'APP_ACCESS_SECRET' => env('APP_ACCESS_SECRET', ''),

    # 区域微服务
    'area' => [
        'HOST'=> env('AREA_HOST', '')
    ],

    # 用户微服务
    'user' => [
        'HOST'=> env('USER_HOST', '')
    ],

    # 短信微服务
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

    # 资源微服务
    'rms' => [
        'HOST'=> env('RMS_HOST', '')
    ],

    # 来公益吧
    'shell' => [
        'HOST'=> env('SHELL_HOST', '')
    ],

    # 问答微服务
    'form' => [
        'HOST'=> env('FORM_HOST', '')
    ],
];
