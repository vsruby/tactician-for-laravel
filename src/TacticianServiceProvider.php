<?php

namespace VinceRuby\Tactician;

use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;

use League\Tactician\CommandBus;
use League\Tactician\Handler\CommandHandlerMiddleware;
use League\Tactician\Handler\CommandNameExtractor\ClassNameExtractor;
use League\Tactician\Handler\MethodNameInflector\HandleInflector;
use League\Tactician\Handler\CommandHandlerMiddleware;

use VinceRuby\Tactician\Contracts\Bus\Dispatcher;
use VinceRuby\Tactician\Locator;

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

        $this->bindDispatcher();
        $this->registerMiddleware();
        $this->registerLocator();
        $this->registerCommandHandler();
        $this->resolveMiddleware();
        $this->registerCommandBus();
        $this->registerDispatcher();
    }

    protected function bindDispatcher()
    {
    	$this->app->bind('VinceRuby/Tactician/Contracts/Bus/Dispatcher', 'VinceRuby/Tactician/Dispatcher');
    }

    protected function registerCommandBus()
    {
        $this->app->bind('tactician.commandbus', function () {

            $middleware = $this->app['tactician.middleware.resolved'];

            return new CommandBus($middleware);
        });
    }

    protected function registerCommandHandler()
    {
        $this->app->bind('tactician.middleware.command_handler', function () {

            return new CommandHandlerMiddleware(
                $this->app['tactician.command_name_extractor'],
                $this->app['tactician.locator'],
                $this->app['tactician.method_name_inflector']
            );

        });

        $this->app->bind('tactician.command_name_extractor', function () {

            return new ClassNameExtractor();

        });

        $this->app->bind('tactician.method_name_inflector', function () {

            return new HandleInflector();

        });

    }

    protected function registerDispatcher()
    {
        $this->app->bind('tactician.dispatcher', function () {

            $bus = $this->app['tactician.commandbus'];

            return new Dispatcher($bus);
        });    	
    }

    protected function registerLocator()
    {        
        $this->app->bind('tactician.locator', function () {

            $config            = $this->app['config'];
            $command_namespace = $config->get('tactician.commandNamespace');
            $handler_namespace = $config->get('tactician.handlerNamespace');

            return new Locator($this->app, $command_namespace, $handler_namespace);
        });    	
    }

    protected function registerMiddleware()
    {
    	//Get middleware set in config file
    	$middlewares = $this->app['config.tactician.middleware'];

    	$names = [];

    	//Loop through retrieved middleware
    	foreach ($middlewares as $middleware) {

    		//Get short name of middleware class and convert to lower
    		$short_name = strtolower(class_basename($middleware));

    		//Bind middleware to name
    		$this->app->bind($short_name, function() use($middleware) {
    			return new '\\' . $middleware();
    		});

    		$names[] = $short_name;

    	}

    	//Add command_handler to end of middleware array
    	$names[] = 'tactician.middleware.command_handler';

    	//Bind all middleware
    	$this->app->bind('tactician.middleware', function() {

    		return new Collection($names);

    	});
    }

    protected function resolveMiddleware()
    {    	
        $this->app->bind('tactician.middleware.resolved', function () {
            return array_map(function ($name) {
                if (is_string($name)) {
                    return $this->app[$name];
                }

                return $name;
            }, $this->app['tactician.middleware']->all());
        });
    }
}