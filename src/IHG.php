<?php


namespace Iapps\Ihg\Rpc;

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
            $service->address = self::$archives[$serviceName] ?? [];

            self::$services[$serviceName] = $service;
            return self::$services[$serviceName];
        }
    }

    public static function register($serviceName, $serviceAddress)
    {
        if (!is_array($serviceAddress)) {
            self::register($serviceName, [$serviceAddress]);
        } else {
            foreach ($serviceAddress as $address) {
                self::$archives[$serviceName][] = $address;
            }
        }
        if (isset(self::$archives[$serviceName]) && is_array(self::$archives[$serviceName])) {
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

    public static function curl($url, $method = '', $header = [], $data = [], $referer = '', $cookie = '', $gzip = false, $returnCookie = 0)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/76.0.3809.100 Safari/537.36');
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
        curl_setopt($curl, CURLOPT_REFERER, $referer);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30);

        if ($method == "POST") {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
        } else {
            curl_setopt($curl, CURLOPT_URL, $url . '?' . http_build_query($data));
        }

        if ($cookie) {
            curl_setopt($curl, CURLOPT_COOKIE, $cookie);
        }

        if ($header) {
            curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        }

        if ($gzip) {
            curl_setopt($curl, CURLOPT_ENCODING, 'gzip');
        }

        curl_setopt($curl, CURLOPT_HEADER, $returnCookie);
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($curl);

        if (curl_errno($curl)) {
            throw new \Exception(curl_error($curl));
        }

        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        return [
            'http_code' => $httpCode,
            'body'      => $data
        ];
    }
}