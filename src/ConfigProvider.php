<?php

namespace Tegic\Security;
/**
 * This file is part of Hyperf Lock.
 *
 * @contact  teg1c@foxmail.com
 */


class ConfigProvider
{
    public function __invoke(): array
    {
        defined('BASE_PATH') or define('BASE_PATH', '');

        return [
            'dependencies' => [],
            'aspects'      => [],
            'annotations' => [
                'scan' => [
                    'paths' => [
                        __DIR__,
                    ],
                ],
            ],
            'commands'  => [],
            'listeners' => [],
            'publish' => [
                [
                    'id'          => 'config',
                    'description' => 'config file of security.',
                    'source'      => __DIR__ . '/../publish/security.php',
                    'destination' => BASE_PATH . '/config/autoload/security.php',
                ]
            ],
        ];
    }
}
