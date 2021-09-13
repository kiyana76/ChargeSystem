<?php

/** @var \Laravel\Lumen\Routing\Router $router */


$router->post('login', 'UserAuthController@login');
$router->post('register/seller', 'UserAuthController@registerSeller');
$router->post('companies', 'CompanyController@create');
$router->get('companies', 'CompanyController@index');
