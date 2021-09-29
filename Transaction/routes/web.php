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

$router->post('payment', 'TransactionController@payment');
$router->post('payment_test', 'TransactionController@payment_test');
$router->get('/zarinpal-callback', 'TransactionController@zarinpalCallback');

$router->get('/', function () use ($router) {
    dd(phpinfo());
    return $router->app->version();
});
