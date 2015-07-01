<?php

namespace VinceRuby\Tactician;

use ArrayAccess;
use ReflectionClass;
use ReflectionParameter;
use Illuminate\Support\Collection;
use League\Tactician\CommandBus;
use VinceRuby\Tactician\Exceptions\MarshalException;

class Dispatcher
{
    /**
     * Instance for the command bus.
     * 
     * @var League\Tactician\CommandBus
     * 
     */
    protected $bus;

    /**
     * Create new instance of Dispatcher.
     * 
     * @param  League\Tactician\CommandBus $bus
     *
     * @return void
     * 
     */
    function __construct(CommandBus $bus)
    {
        $this->bus = $bus;
    }

    /**
     * Dispatch a command to its respective handler.
     * 
     * @param  mixed $command
     * 
     * @return mixed
     * 
     */
    public function dispatch($command)
    {
        return $this->bus->handle($command);
    }

    /**
     * Marshal a command and dispatch it to its respective handler.
     * 
     * @param  mixed       $command
     * @param  ArrayAccess $source
     * @param  array       $extras
     * 
     * @return mixed
     * 
     */
    public function dispatchFrom($command, ArrayAccess $source, array $extras = [])
    {
        return $this->dispatch($this->marshal($command, $source, $extras));
    }

    /**
     * Marshal a command and dispatch it to its respective handler.
     *
     * @param  mixed  $command
     * @param  array  $array
     * 
     * @return mixed
     * 
     */
    public function dispatchFromArray($command, array $array)
    {
        return $this->dispatch($this->marshalFromArray($command, $array));
    }

    /**
     * Marshal a command from the given array accessible object.
     * 
     * @param  string      $command
     * @param  ArrayAccess $source
     * @param  array       $extras
     * 
     * @return mixed
     * 
     */
    protected function marshal($command, ArrayAccess $source, array $extras = [])
    {
        $injected   = [];
        $reflection = new ReflectionClass($command);

        if ($constructor = $reflection->getConstructor()) {

            $injected = array_map(function ($parameter) use ($command, $source, $extras) {

                return $this->getParameterValueForCommand($command, $source, $parameter, $extras);

            }, $constructor->getParameters());

        }

        return $reflection->newInstanceArgs($injected);        
    }

    /**
     * Marshal a command from the given array.
     *
     * @param  string $command
     * @param  array  $array
     * 
     * @return mixed
     * 
     */
    protected function marshalFromArray($command, array $array)
    {
        return $this->marshal($command, new Collection, $array);
    }

    /**
     * Get a parameter value for a marshaled command.
     *
     * @param  string              $command
     * @param  ArrayAccess         $source
     * @param  ReflectionParameter $parameter
     * @param  array               $extras
     * 
     * @return mixed
     * 
     */
    protected function getParameterValueForCommand($command, ArrayAccess $source, ReflectionParameter $parameter, array $extras = [])
    {
        if (array_key_exists($parameter->name, $extras)) {

            return $extras[$parameter->name];

        }

        if (isset($source[$parameter->name])) {

            return $source[$parameter->name];

        }

        if ($parameter->isDefaultValueAvailable()) {

            return $parameter->getDefaultValue();

        }
        
        MarshalException::whileMapping($command, $parameter);
    }
}
