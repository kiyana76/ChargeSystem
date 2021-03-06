<?php

namespace App\Providers;

use App\Classes\CreateCharge\CreateChargeFactoryInterface;
use App\Classes\CreateCharge\UuIdV1;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        app('validator')->extend(
            'valid_mobile',
            'App\Rules\IranianMobileNumber@passes'
        );
        $this->app->register(RepositoryServiceProvider::class);
        $this->app->bind(CreateChargeFactoryInterface::class, UuIdV1::class);
    }
}
