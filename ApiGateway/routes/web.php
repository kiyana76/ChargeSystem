<?php

/** @var \Laravel\Lumen\Routing\Router $router */


/************************* Start UserService API**************/
$router->post('login', 'UserService\AuthController@login');
$router->post('register/user', 'UserService\AuthController@register');

$router->post('companies', 'UserService\CompanyController@create');
$router->get('companies', 'UserService\CompanyController@index');

$router->get('credit/log', 'UserService\CreditController@log');
$router->post('credit', 'UserService\CreditController@create');

$router->get('get-credit', 'UserService\CreditController@show');
/************************* End UserService API**************/

/************************* Start ChargeService API**************/
$router->get('charge-categories', 'ChargeService\ChargeController@chargeCategoryIndex');
$router->post('charge/demand', 'ChargeService\ChargeController@chargeDemand');
$router->get('charge', 'ChargeService\ChargeController@index');
$router->post('charge/burnt', 'ChargeService\ChargeController@burnt');
/************************* End ChargeService API**************/


$router->get('/', function () use ($router) {
    return $router->app->version();
});
