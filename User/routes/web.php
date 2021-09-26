<?php

/** @var \Laravel\Lumen\Routing\Router $router */


$router->post('admin/login', 'UserAuthController@login');
$router->post('register/user', ['uses' =>'UserAuthController@registerUser', 'middleware' => 'auth']);
$router->get('user/{id}', 'UserController@show');
$router->get('get-user', ['uses' =>'UserController@get'/*, 'middleware' => 'auth'*/]);
$router->get('get-customer', ['uses' =>'CustomerController@get'/*, 'middleware' => 'auth'*/]);

$router->post('companies', 'CompanyController@create');
$router->get('companies', 'CompanyController@index');

$router->post('customer/login', 'CustomerController@login');

$router->post('credit', 'CreditController@create');
$router->put('credit', 'CreditController@update');
$router->get('get-credit', 'CreditController@getCredit');
$router->get('credit/log', 'CreditController@log');
$router->get('/', function () use ($router) {
    return $router->app->version();
});
