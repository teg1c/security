<?php

declare(strict_types=1);
/**
 * This file is part of Security.
 *
 * @Author     Tegic
 * @Contact  https://github.com/teg1c
 */
return [
    'default' => [
        'driver' => \Tegic\Security\Driver\AliGreenDriver::class,
        'config' => [
            'access_key_id' => env('ALI_ACCESS_KEY_ID'),
            'access_key_secret' => env('ALI_ACCESS_KEY_SECRET'),
            'region_id' => env('ALI_REGION_ID', 'cn-shanghai'),
            'debug' => false,
        ],
    ],
    'tencent' => [
        'driver' => \Tegic\Security\Driver\TencentDriver::class,
        'config' => [
            'access_key_id' => env('TENCENT_ACCESS_KEY_ID'),
            'access_key_secret' => env('TENCENT_ACCESS_KEY_SECRET'),
            'region_id' => env('TENCENT_REGION_ID', 'cn-shanghai'),
            'level' => [],
        ],
    ],
];
