<?php

namespace VinceRuby\Tactician;

use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;

use League\Tactician\CommandBus;
use League\Tactician\Handler\CommandHandlerMiddleware;

use VinceRuby\Tactician\Contracts\Bus\Dispatcher;
use VinceRuby\Tactician\Locator;

class TacticianServiceProvider extends ServiceProvider
{
    /**
     * Boot the service provider.
     *
     * @return void
     * 
     */
	public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/config.php' => config_path('tactician.php')
        ], 'config');

        $this->bootBindings();
    }

    /**
     * Register the service provider.
     *
     * @return void
     * 
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/config.php', 'tactician');

        $this->registerLocator();
        $this->registerExtractor();
        $this->registerInflector();
        $this->registerCommandHandler();
        $this->registerMiddleware();
        $this->registerCommandBus();
        $this->registerDispatcher();
    }

    /**
     * Bind some interfaces and implementations.
     *
     * @return void
     * 
     */
    protected function bootBindings()
    {
    	$this->app['League\Tactician\CommandBus'] = function($app) {
    		return $app['tactician.commandbus'];
    	}

    	$this->app['League\Tactician\Handler\CommandHandlerMiddleware'] = function($app) {
    		return $app['tactician.handler'];
    	}

    	$this->app['League\Tactician\Handler\CommandNameExtractor\CommandNameExtractor'] = function($app) {
    		return $app['tactician.extractor'];
    	}

    	$this->app['League\Tactician\Handler\MethodNameInflector\MethodNameInflector'] = function($app) {
    		return $app['tactician.inflector'];
    	}

    	$this->app['League\Tactician\Handler\Locator\HandlerLocator'] = function($app) {
    		return $app['tactician.locator'];
    	}

    	$this->app['VinceRuby\Tactician\Contracts\Bus\Dispatcher'] = function($app) {
    		return $app['tactician.dispatcher'];
    	}
    }

    /**
     * Register bindings for the Command Handler.
     * 
     * @return void
     * 
     */
    public function registerCommandBus()
    {
    	$this->app['tactician.commandbus'] = $this->app->share(function($app) {

    		return new CommandBus($app['tactician.middleware']);

    	});
    }

    /**
     * Register bindings for the Command Handler.
     * 
     * @return void
     * 
     */
    public function registerCommandHandler()
    {
    	$this->app['tactician.handler'] = $this->app->share(function($app) {

    		return new CommandHandlerMiddleware(
    			$app['tactician.extractor'],
    			$app['tactician.locator'],
    			$app['tactician.inflector']
    		);

    	});
    }


    /**
     * Register bindings for the Dispatcher.
     * 
     * @return void
     * 
     */
    public function registerDispatcher()
    {
    	$this->app['tactician.dispatcher'] = $this->app->share(function($app) {

    		return new Dispatcher($app['tactician.middleware']);

    	});
    }

    /**
     * Register bindings for the Command Name Extractor.
     * 
     * @return void
     * 
     */
    protected function registerExtractor()
    {
    	$this->app['tactician.extractor'] = $this->app->share(function($app) {

    		return $app->make($this->config('extractor'));

    	});    	
    }

    /**
     * Register bindings for the Method Name Inflector.
     * 
     * @return void
     * 
     */
    protected function registerInflector()
    {
    	$this->app['tactician.inflector'] = $this->app->share(function($app) {

    		return $app->make($this->config('inflector'));

    	});    	
    }

    /**
     * Register bindings for the Handler Locator.
     * 
     * @return void
     * 
     */
    protected function registerLocator()
    {
    	$this->app['tactician.locator'] = $this->app->share(function($app) {

    		$command_namespace = $this->config('command_namespace');
    		$handler_namespace = $this->config('handler_namespace');
    		$locator           = $this->config('locator');

    		return $app->make($locator, [$this->app, $command_namespace, $handler_namespace]);
    	});
    }


    /**
     * Register bindings for all the middleware.
     * 
     * @return void
     * 
     */
    protected function registerMiddleware()
    {
    	$this->app->bind('tactician.middleware', function() {

	    	$middleware = $this->config('middleware');

	    	$resolved   = array_map(function($name) {

	    		return new $name();

	    	}, $middleware);

	    	$resolved[] = $this->app['tactician.handler'];

	    	return $resolved;

	    });
    }

    /**
     * Helper to get the config values
     *
     * @param  string $key
     * 
     * @return string
     * 
     */
    protected function config($key, $default = null)
    {
        return config('tactician.' . $key, $default);
    }
}