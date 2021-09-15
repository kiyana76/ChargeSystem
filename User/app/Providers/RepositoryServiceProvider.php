<?php

namespace App\Providers;

use App\Repository\CustomerRepositoryInterface;
use App\Repository\Eloquent\CreditEloquentRepository;
use App\Repository\CreditRepositoryInterface;
use App\Repository\Eloquent\CustomerEloquentRepository;
use App\Repository\Eloquent\UserEloquentRepository;
use App\Repository\UserRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(UserRepositoryInterface::class, UserEloquentRepository::class);
        $this->app->bind(CreditRepositoryInterface::class, CreditEloquentRepository::class);
        $this->app->bind(CustomerRepositoryInterface::class, CustomerEloquentRepository::class);
    }

    /**
     * Boot the authentication services for the application.
     *
     * @return void
     */
    public function boot()
    {

    }
}
