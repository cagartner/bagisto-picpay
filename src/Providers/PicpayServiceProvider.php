<?php

namespace Cagartner\Picpay\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * Class PicpayServiceProvider
 * @package Cagartner\Pagseguro\Providers
 */
class PicpayServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        include __DIR__ . '/../Http/routes.php';

        $this->mergeConfigFrom(
            dirname(__DIR__) . '/Config/system.php', 'core'
        );

        $this->app->register(ModuleServiceProvider::class);

        $this->mergeConfigFrom(
            dirname(__DIR__) . '/Config/paymentmethods.php', 'paymentmethods'
        );

        $this->loadMigrationsFrom(__DIR__ .'/../Database/Migrations');
    }
}
