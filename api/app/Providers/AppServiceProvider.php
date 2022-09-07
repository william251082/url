<?php

namespace App\Providers;

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
        $this->app->singleton(UrlShortenerServiceProvider::class, function ($app) {
            $config = ['debug' => true];
            return new UrlShortenerServiceProvider($config);
        });
    }
}
