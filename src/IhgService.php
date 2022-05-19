<?php

namespace Iapps\Ihg\Rpc;


class IhgService
{
    protected static $services;
    protected static $instances = [];

    public static function init($services)
    {
        self::$services = $services;
    }

    public static function instance($name): IhgService
    {
        $name = ucfirst(strtolower($name));

        if (empty(self::$instances[$name])) {
            $service = "{$name}Service";
            if (class_exists($service)) {
                self::$instances[$name] = new $service();
            } else {
                self::$instances[$name] = new Rpc($name);
            }

        }
        return self::$instances[$name];
    }

    public function call($route, $params, $headers = [])
    {
        $url = self::$services[$this->service];

        return [self::$services, $url, $route, $params];

    }

}
