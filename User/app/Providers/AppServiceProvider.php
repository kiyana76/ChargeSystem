<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
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
    }

    public function boot() {
        Gate::policy('App\Models\User', 'App\Policies\User');
    }
}
