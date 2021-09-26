<?php

namespace App\Providers;

use App\Repositories\Eloquent\OrderEloquentRepository;
use App\Repositories\OrderRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(OrderRepositoryInterface::class, OrderEloquentRepository::class);
    }
}
