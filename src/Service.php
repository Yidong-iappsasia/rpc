<?php


namespace Iapps\Ihg\Rpc;


class Service
{

    public $name;
    public $address;

    private $class;
    private $action;

    public function __construct($name)
    {

    }

    public function __get($name)
    {
        $this->class = $name;
        return $this;
    }

    public function __call($name, $arguments)
    {
        $this->action = $name;
        $route        = '/rpc';

        $params = [
            'class'  => $this->class,
            'action' => $this->action,
            'params' => $arguments
        ];

        return $this->call($route, $params);
        // ihg_rpc_client
    }

    public function call($route, $params, $method = 'POST', $headers = [])
    {
        $getAllHeaders = function () {
            $headers = [];
            if (is_array($_SERVER)) {
                foreach ($_SERVER as $name => $value) {
                    if (substr($name, 0, 5) == 'HTTP_') {
                        $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
                    }
                }
            }
            return $headers;
        };
        $allHeaders    = $getAllHeaders();
        if (is_array($headers) && $headers) {
            foreach ($headers as $key => $header) {
                $allHeaders[$key] = $header;
            }
        }

        if (empty($this->address[0])) {
            throw new \Exception($this->name . ' service not registered.', '404');
        }

        $index = rand(0, count($this->address) - 1);
        $url   = $this->address[$index];
        $url   = rtrim($url, '/');
        $route = ltrim($route, '/');
        $url   = $url . '/' . $route;

        try {
            $response = IHG::curl($url, $method, $allHeaders, $params);
            if ($response['http_code'] == 200) {
                return $response['body'];
            } else {
                throw new \Exception($response['body'], $response['http_code']);
            }
        } catch (\Exception $exception) {
            throw  $exception;
        }
    }

}