<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/
$router->post('login', 'UserService\AuthController@login');
$router->post('register/user', 'UserService\AuthController@register');

$router->post('companies', 'UserService\CompanyController@create');
$router->get('companies', 'UserService\CompanyController@index');

$router->get('credit/log', 'UserService\CreditController@log');
$router->post('credit', 'UserService\CreditController@create');

$router->get('get-credit', 'UserService\CreditController@show');

$router->get('/', function () use ($router) {
    return $router->app->version();
});
