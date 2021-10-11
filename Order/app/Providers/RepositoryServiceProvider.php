<?php

namespace App\Providers;

use App\Repositories\Elastic\SearchElasticRepository;
use App\Repositories\Eloquent\OrderEloquentRepository;
use App\Repositories\Eloquent\SearchEloquentRepository;
use App\Repositories\OrderRepositoryInterface;
use App\Repositories\SearchRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(OrderRepositoryInterface::class, OrderEloquentRepository::class);
        if (config('elasticquent.config.status') == 'enable')
            $this->app->bind(SearchRepositoryInterface::class, SearchElasticRepository::class);
        else
            $this->app->bind(SearchRepositoryInterface::class, SearchEloquentRepository::class);
    }
}
