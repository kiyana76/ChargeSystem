<?php

/** @var \Laravel\Lumen\Routing\Router $router */


$router->post('orders', 'OrderController@create');
$router->put('orders', 'OrderController@update');
$router->get('orders', 'OrderController@index');
$router->get('orders/charge-details', 'OrderController@indexWithChargeDetails');

$router->get('/', function () use ($router) {
    \App\Models\Order::createIndex($shards = null, $replicas = null);
    $orders = \App\Models\Order::with('orderItem')->get();
    $orders->addToIndex();
    return $router->app->version();
});
