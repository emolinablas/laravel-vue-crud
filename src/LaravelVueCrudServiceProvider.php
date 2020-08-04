<?php

namespace Emolinablas\LaravelVueCrud;

use Illuminate\Support\ServiceProvider;

class LaravelVueCrudServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/views', 'laravel-vue-crud');

        $this->publishes([
                             __DIR__ . '/public' => public_path('vendor/emolinablas/laravel-vue-crud'),
                         ], 'public');
    }
}
