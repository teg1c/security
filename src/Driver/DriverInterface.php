<?php

declare(strict_types=1);
/**
 * This file is part of Security.
 *
 * @Author     Tegic
 * @Contact  https://github.com/teg1c
 */
namespace Tegic\Security\Driver;

interface DriverInterface
{

    public function text($content = '');

    public function setOption(array $options = []);
}
