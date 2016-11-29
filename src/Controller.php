<?php namespace Glue\Mvc;

use Glue\App;

abstract class Controller
{
    protected static $app;

    public static function setApp(App $app)
    {
        static::$app = $app;
    }

    public function __call($method, array $args)
    {
        return call_user_func_array([static::$app, $method], $args);
    }

    public function __get($key)
    {
        if (static::$app->isAlias($key)) {
            return static::$app->make($key);
        }

        throw new \Exception(__CLASS__ . " has no property called $key");
    }
}