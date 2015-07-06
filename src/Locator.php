<?php

namespace VinceRuby\Tactician;

use Illuminate\Contracts\Container\Container;
use League\Tactician\Exception\MissingHandlerException;
use League\Tactician\Handler\Locator\HandlerLocator;

class Locator implements HandlerLocator
{
    /**
     * The container instance.
     * 
     * @var Illuminate\Contracts\Container\Container
     * 
     */
    protected $container;

    /**
     * Base namespace for commands.
     * 
     * @var string
     * 
     */
    protected $command_namespace;

    /**
     * Base namespace for command handlers.
     * 
     * @var string
     * 
     */
    protected $handler_namespace;

    /**
     * Create new instance of Locator.
     * 
     * @param  Illuminate\Contracts\Container\Container $container
     * @param  string                                   $command_namespace
     * @param  string                                   $handler_namespace
     *
     * @return void
     * 
     */
    public function __construct(Container $container, $command_namespace, $handler_namespace)
    {
        $this->container         = $container;
        $this->command_namespace = $command_namespace;
        $this->handler_namespace = $handler_namespace;
    }

    /**
     * Attempts to find the command's respective handler.
     *
     * @param  string $command_name
     *
     * @return mixed
     * 
     * @throws MissingHandlerException
     *
     */
    public function getHandlerForCommand($command_name)
    {
        $command     = str_replace($this->command_namespace, '', $command_name);
        $handlerName = $this->handler_namespace.'\\'.trim($command, '\\').'Handler';

        if (!class_exists($handlerName)) {

            throw MissingHandlerException::forCommand($command_name);

        }

        $handler = $this->container->make($handlerName);
        
        return $handler;
    }
}