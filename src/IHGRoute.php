<?php


namespace Iapps\Ihg\Rpc;

class IHGRoute
{
    private static $services = [];
    private $useService;
    private $route;
    private $headers;
    private $url;
    private $params;
    private $method = 'GET';

    public static function request($url, $method, $headers, $params)
    {
        try {
            $response = IHG::curl($url, $method, $headers, $params);
            if ($response['http_code'] == 200) {
                return $response['body'];
            } else {
                throw new \Exception($response['body'], $response['http_code']);
            }
        } catch (\Exception $exception) {
            throw  $exception;
        }
    }

    private function signature($data, $signature_pwd = '')
    {
        $timestamp = explode(" ", microtime())[1];

        $md5       = strtolower(md5(json_encode($data)));
        $signature = strtolower(md5($md5 . "|" . $signature_pwd . "|" . $timestamp));

        return [$timestamp, $signature];
    }

    public function setService(String $name, String $ip)
    {
        $ip       = trim(trim($ip, ' '), '/');
        $services = self::$services[$name] ?? [];
        if (!in_array($ip, $services)) {
            self::$services[$name][] = $ip;
        }
    }

    public function getService()
    {
        return self::$services;
    }

    public function useService($name, $ip = '')
    {
        if ($ip) {
            $this->setService($name, $ip);
        }
        if (empty(self::$services[$name])) {
            throw new \Exception(sprintf("service [%s] not set.", $name));
        }
        $this->useService = $name;
        return $this;
    }

    public function getUseService()
    {
        return $this->useService;
    }

    public function setRoute($route)
    {
        if ($route{0} !== '/') {
            throw new \Exception(sprintf("route first char must be '/'."));
        }
        $this->route = $route;
        return $this;
    }

    public function getRoute()
    {
        return $this->route;
    }

    public function setHeaders($headers = [])
    {
        $this->headers = $headers;
        return $this;
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function setParams($params = [])
    {
        $this->params = $params;
        return $this;
    }

    public function getParams()
    {
        return $this->params;
    }

    public function setMethod($method)
    {
        $this->method = $method;
        return $this;
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function call()
    {
        $route = $this->getRoute();
        if (empty($route)) {
            throw new \Exception(sprintf("route is empty."));
        }
        $use = $this->getUseService();
        if (empty($use)) {
            throw new \Exception(sprintf("service to be used is not set."));
        }
        $services = self::$services;
        if (empty($services[$use])) {
            throw new \Exception(sprintf("service to be used is not set."));
        }
        $service = $services[$use];
        $address = $service[0];
        $url     = $address . $route;
        $params  = $this->params;
        $method  = $this->method;
        $headers = $this->headers;

        list($timestamp, $signature) = $this->signature($params);
        $params['timestamp'] = $timestamp;
        $params['signature'] = $signature;

        return self::request($url, $method, $headers, $params);
    }
}