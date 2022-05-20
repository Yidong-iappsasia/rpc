<?php


namespace Iapps\Ihg\Rpc;


class Service
{

    public $name;
    public $address;

    public function __construct($name)
    {

    }

    public function __get($name)
    {
        print_r($name);
        return $this;
    }

    public function __call($name, $arguments)
    {
        print_r([$name, $arguments]);

        return $this;
        // ihg_rpc_client
    }

    public function call($route, $params, $method = 'POST', $headers = [])
    {

    }

}