<?php

/** @var \Laravel\Lumen\Routing\Router $router */


$router->post('admin/login', 'UserAuthController@login');
$router->post('register/user', ['uses' =>'UserAuthController@registerUser', 'middleware' => 'auth']);
$router->get('user/{id}', 'UserController@show');

$router->post('companies', 'CompanyController@create');
$router->get('companies', 'CompanyController@index');

$router->post('customer/login', 'CustomerController@login');
