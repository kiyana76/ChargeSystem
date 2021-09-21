<?php

namespace App\Providers;


use App\Repository\ChargeCategoryRepositoryInterface;
use App\Repository\ChargeRepositoryInterface;
use App\Repository\Eloquent\ChargeCategoryEloquentRepository;
use App\Repository\Eloquent\ChargeEloquentRepository;
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
        $this->app->bind(ChargeCategoryRepositoryInterface::class, ChargeCategoryEloquentRepository::class);
        $this->app->bind(ChargeRepositoryInterface::class, ChargeEloquentRepository::class);
    }
}
