<?php

namespace VinceRuby\Tactician\Tests\Exceptions;

use ReflectionClass;
use PHPUnit_Framework_TestCase;

use VinceRuby\Tactician\Exceptions\MarshalException;
use VinceRuby\Tactician\Tests\Stubs\TestCommand;

class MarshalExceptionTest extends PHPUnit_Framework_TestCase
{
	/**
	 * 
	 * @test
	 *
	 * @expectedException        \VinceRuby\Tactician\Exceptions\MarshalException
	 * @expectedExceptionMessage Unable to map parameter [data] to command [VinceRuby\Tactician\Tests\Stubs\TestCommand]
	 * 
	 */
	public function it_should_return_debugging_info()
	{
		$reflection = new ReflectionClass(TestCommand::class);

		$parameters  = $reflection->getConstructor()->getParameters();

		MarshalException::whileMapping(TestCommand::class, $parameters[0]);
	}
}