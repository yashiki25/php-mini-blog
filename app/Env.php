<?php

namespace App;

require 'vendor/autoload.php';

use Dotenv\Dotenv;

class Env
{
    private static Dotenv $dotEnv;

    public static function get($key)
    {
        self::$dotEnv = Dotenv::createImmutable('../../');
        self::$dotEnv->load();

        return array_key_exists($key, $_ENV) ? $_ENV[$key] : null;
    }
}