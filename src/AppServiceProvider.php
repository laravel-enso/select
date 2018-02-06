<?php

namespace LaravelEnso\Select;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__.'/resources/assets/js' => resource_path('assets/js'),
        ], 'select-assets');

        $this->publishes([
            __DIR__.'/resources/assets/js' => resource_path('assets/js'),
        ], 'enso-assets');
    }
}
