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
            __DIR__.'/../resources/assets/js/components/core' => base_path('resources/assets/js/components/core'),
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
