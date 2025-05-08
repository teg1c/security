<?php

declare(strict_types=1);
/**
 * This file is part of Security.
 *
 * @Author     Tegic
 * @Contact  https://github.com/teg1c
 */
namespace Tegic\Security;

use Hyperf\Contract\ConfigInterface;
use Hyperf\Utils\ApplicationContext;
use InvalidArgumentException;
use Psr\Container\ContainerInterface;
use Tegic\Security\Driver\AliGreenDriver;
use Tegic\Security\Driver\DriverInterface;
use Tegic\Security\Driver\TencentDriver;
use Tegic\Security\Exception\SecurityException;

class Security
{
    public static function instance($driver = 'default',$config = [])
    {
        if (class_exists(ApplicationContext::class)){
            return self::hyperf($driver);
        }
        return self::getInstance($driver,$config);
    }
    /**
     * @param $driver
     * @return DriverInterface
     */
    private static function hyperf($driver): DriverInterface
    {
        /** @var ContainerInterface $container */
        $container = ApplicationContext::getContainer();
        /** @var ConfigInterface $config */
        $config = $container->get(ConfigInterface::class);

        if (! $config->has("security.{$driver}")) {
            throw new InvalidArgumentException(sprintf('The lock security %s is invalid.', $driver));
        }

        $driverClass = $config->get("security.{$driver}.driver", AliGreenDriver::class);
        $config = $config->get("security.{$driver}.config", []);

        return make($driverClass, [$config]);
    }

    /**
     * @param string $driverName
     * @param array $config
     * @return DriverInterface
     * @throws SecurityException
     */
    private static function getInstance($driverName = 'ali', $config = []): DriverInterface
    {
        switch ($driverName) {
            case 'ali':
                $driverClass = new AliGreenDriver($config);
                break;
            case 'tencent':
                $driverClass = new TencentDriver($config);
                break;
            default:
                throw new SecurityException('channel error');
        }
        return $driverClass;
    }
}
