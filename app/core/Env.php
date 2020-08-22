<?php

namespace App;

use Dotenv\Dotenv;

class Env
{
    private static Dotenv $dotEnv;

    public static function get($key)
    {
        if ((self::$dotEnv instanceof Dotenv) === false) {
            self::$dotEnv = Dotenv::create('../');
            self::$dotEnv->load();
        }

        return array_key_exists($key, $_ENV) ? $_ENV[$key] : null;
    }
}