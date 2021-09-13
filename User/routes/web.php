<?php

/** @var \Laravel\Lumen\Routing\Router $router */


$router->post('admin/login', 'UserAuthController@login');
$router->post('register/seller', ['uses' =>'UserAuthController@registerSeller', 'middleware' => 'auth']);
$router->post('companies', 'CompanyController@create');
$router->get('companies', 'CompanyController@index');
