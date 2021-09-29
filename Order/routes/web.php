<?php

/** @var \Laravel\Lumen\Routing\Router $router */


$router->post('orders', 'OrderController@create');
$router->put('orders', 'OrderController@update');
$router->get('orders', 'OrderController@index');

$router->get('/', function () use ($router) {
    return $router->app->version();
});
