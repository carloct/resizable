<?php

namespace Keisen\Resizable;

use Illuminate\Support\ServiceProvider;

class ResizableServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * The boot method.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes(
            [__DIR__.'/../config/resizable.php' => config_path('resizable.php')]
        );


        $this->app['events']->listen('eloquent.saving*', function ($model) {
            if ($model instanceof Resizable && $model->hasMedia()) {
                $model->resize();
            }
        });
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/resizable.php', 'resizable');
    }

}