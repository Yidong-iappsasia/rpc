<?php

require_once('../src/IHGRoute.php');
require_once('../src/IHG.php');

$route = new \Iapps\Ihg\Rpc\IHGRoute();

$route->setService('account_service', 'https://api.dev.iappshealth.com/account_service');

try {
    $response = $route->useService('account_service', 'https://api.dev.iappshealth.com/account_service')
        ->setRoute('/service/user/get')
        ->setHeaders(['X-app: OCRT3XIxeAoWjjCC0pP5'])
        ->setParams(['user_id' => 'a913a817-e9c6-4930-9b9a-918842b7f50d'])
        ->call();
    print_r($response);
} catch (\Exception $exception) {

    echo $exception->getMessage();
}