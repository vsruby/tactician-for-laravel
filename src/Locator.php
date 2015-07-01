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
    protected $commandNamespace;

    /**
     * Base namespace for command handlers.
     * 
     * @var string
     * 
     */
    protected $handlerNamespace;

    /**
     * Create new instance of Locator.
     * 
     * @param  Illuminate\Contracts\Container\Container $container
     * @param  string                                   $commandNamespace
     * @param  string                                   $handlerNamespace
     *
     * @return void
     * 
     */
    public function __construct(Container $container, $commandNamespace, $handlerNamespace)
    {
        $this->container        = $container;
        $this->commandNamespace = $commandNamespace;
        $this->handlerNamespace = $handlerNamespace;
    }

    /**
     * Attempts to find the command's respective handler.
     *
     * @param  string $commandName
     *
     * @return mixed
     * 
     * @throws MissingHandlerException
     *
     */
    public function getHandlerForCommand($commandName)
    {
        $command     = str_replace($this->commandNamespace, '', $commandName);
        $handlerName = $this->handlerNamespace.'\\'.trim($command, '\\').'Handler';

        if (!class_exists($handlerName)) {

            throw MissingHandlerException::forCommand($commandName);

        }

        $handler = $this->container->make($handlerName);
        
        return $handler;
    }
}