<?php

namespace App\Providers;

use App\Service\AuthService;
use App\Service\OrderService;
use App\Service\TokenService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        app()->bind(TokenService::class, TokenService::class);
        app()->bind(AuthService::class, AuthService::class);
        app()->bind(OrderService::class, OrderService::class);
    }

    public function boot()
    {
        //
    }
}
