<?php

namespace VinceRuby\Tactician\Tests;

use Mockery;
use PHPUnit_Framework_TestCase;
use VinceRuby\Tactician\Tests\Stubs\TestCommand;

use VinceRuby\Tactician\Dispatcher;

class CommandExecutionTest extends PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		//
	}

	public function tearDown()
	{
		//
	}

	/**
	 * 
	 * @test
	 * 
	 */
	public function it_should_return_true_for_dispatch()
	{
		$this->assertTrue(true);
	}
}