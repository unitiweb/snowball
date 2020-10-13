<?php

namespace Snowball;

use Symfony\Component\Yaml\Yaml;

/**
 * Class Config
 * @package Snowball
 */
class Config
{
    const PATH = __DIR__ . '../config.yml';

    /**
     * @return array
     */
    public static function load(): array
    {
        $config = Yaml::parseFile(self::PATH);
        dd($config);

        return $config;
    }
}
