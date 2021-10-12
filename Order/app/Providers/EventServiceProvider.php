<?php

namespace App\Providers;

use App\Events\CreateOrderEvent;
use App\Events\UpdateOrderEvent;
use App\Listeners\CreateOrderInElasticListener;
use App\Listeners\UpdateOrderInElasticListener;
use Laravel\Lumen\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        CreateOrderEvent::class => [
            CreateOrderInElasticListener::class
        ],
        UpdateOrderEvent::class => [
            UpdateOrderInElasticListener::class
        ],
    ];
}
