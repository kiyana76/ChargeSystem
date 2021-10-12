<?php

namespace App\Providers;

use App\Repository\Eloquent\TransactionEloquentRepository;
use App\Repository\TransactionRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(TransactionRepositoryInterface::class, TransactionEloquentRepository::class);
    }

}
