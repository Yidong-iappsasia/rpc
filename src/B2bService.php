<?php


namespace Iapps\Ihg\Rpc;


class B2bService extends Rpc
{
    public function __construct($service = '')
    {
        parent::__construct($service);
        $this->service = 'B2b';
    }

}
