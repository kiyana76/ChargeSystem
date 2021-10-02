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
$router->post('login', 'AuthController@login');
$router->get('charge-categories', 'ChargeCategoryController@index');
$router->post('orders', 'OrderController@create');
$router->get('orders', 'OrderController@index');
$router->post('charge/burnt', 'ChargeController@burnt');
$router->get('charge', 'ChargeController@index');
$router->get('transactions', 'TransactionController@index');
$router->get('/', function () use ($router) {
    return $router->app->version();
});
