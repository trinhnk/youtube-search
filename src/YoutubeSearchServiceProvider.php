<?php

namespace Trinhnk\YoutubeSearch;

use Illuminate\Support\ServiceProvider;

class YoutubeSearchServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot(): void
    {
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'trinhnk');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'trinhnk');
        // $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        // $this->loadRoutesFrom(__DIR__.'/routes.php');

        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/youtube-search.php', 'youtube-search');

        // Register the service the package provides.
        $this->app->singleton('youtube-search', function ($app) {
            return new YoutubeSearch;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['youtube-search'];
    }

    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole(): void
    {
        // Publishing the configuration file.
        $this->publishes([
            __DIR__.'/../config/youtube-search.php' => config_path('youtube-search.php'),
        ], 'youtube-search.config');

        // Publishing the views.
        /*$this->publishes([
            __DIR__.'/../resources/views' => base_path('resources/views/vendor/trinhnk'),
        ], 'youtube-search.views');*/

        // Publishing assets.
        /*$this->publishes([
            __DIR__.'/../resources/assets' => public_path('vendor/trinhnk'),
        ], 'youtube-search.views');*/

        // Publishing the translation files.
        /*$this->publishes([
            __DIR__.'/../resources/lang' => resource_path('lang/vendor/trinhnk'),
        ], 'youtube-search.views');*/

        // Registering package commands.
        // $this->commands([]);
    }
}
