<?php

namespace LaravelEnso\Select;

use Illuminate\Support\ServiceProvider;

class SelectServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../resources/assets/js/components' => resource_path('assets/js/components/laravel-enso'),
        ], 'select-component');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
