<?php


namespace Iapps\Ihg\Rpc;


use Grpc\Server;

class IHG
{
    private static $archives = [];
    private static $services = [];

    public static function service($serviceName, $serviceAddress = ''): Service
    {
        if ($serviceAddress) {
            self::register($serviceName, $serviceAddress);
        }
        if (isset(self::$services[$serviceName]) && self::$services[$serviceName] instanceof Service) {
            return self::$services[$serviceName];
        } else {
            $service = new Service($serviceName);

            $service->name    = $serviceName;
            $service->address = self::$archives[$serviceName];

            self::$services[$serviceName] = $service;
            return self::$services[$serviceName];
        }
    }

    public static function register($serviceName, $serviceAddress)
    {
        if (!is_array($serviceAddress)) {
            self::register($serviceName, [$serviceAddress]);
        }
        foreach ($serviceAddress as $address) {
            self::$archives[$serviceName][] = $address;
        }
        if (is_array(self::$archives[$serviceName])) {
            self::$archives[$serviceName] = array_unique(self::$archives[$serviceName]);
        }
        return self::discover($serviceName, true);
    }

    public static function discover($serviceName, $all = false)
    {
        if (!empty(self::$archives[$serviceName])) {
            if ($all) {
                return self::$archives[$serviceName];
            } else {
                return self::$archives[rand(0, count(self::$archives) - 1)];
            }
        }
        return null;
    }



}