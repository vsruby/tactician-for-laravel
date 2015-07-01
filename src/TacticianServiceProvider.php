<?php

namespace VinceRuby\Tactician;

use Illuminate\Support\ServiceProvider;

class TacticianServiceProvider extends ServiceProvider
{
    /**
     * Boot service provider.
     *
     * @return void
     */
	public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/config.php' => config_path('tactician.php'),
        ], 'config');
    }

    /**
     * Register service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/config.php', 'tactician');

        $this->registerDispatcher();
    }

    protected function registerDispatcher()
    {
    	$this->app->bind('VinceRuby/Tactician/Contracts/Bus/Dispatcher', 'VinceRuby/Tactician/Dispatcher');
    }
}