<?php

namespace VinceRuby\Tactician\Tests;

use Mockery;
use PHPUnit_Framework_TestCase;

use Illuminate\Support\Collection;

use League\Tactician\CommandBus;

use VinceRuby\Tactician\Tests\Stubs\TestCommand;
use VinceRuby\Tactician\Tests\Stubs\TestDispatcher;
use VinceRuby\Tactician\Tests\Stubs\TestWithDefaultCommand;

class DispatcherTest extends PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		$this->bus        = Mockery::mock(CommandBus::class);
		$this->dispatcher = new TestDispatcher($this->bus);
	}

	public function tearDown()
	{
		Mockery::close();
	}

	/**
	 * 
	 * @test
	 * 
	 */
	public function it_should_return_foo_using_dispatch()
	{
		$this->bus->shouldReceive('handle')->andReturn('foo');

		$command = new TestWithDefaultCommand();

		$this->assertEquals($this->dispatcher->dispatch($command), 'foo');
	}

	/**
	 * 
	 * @test
	 * 
	 */
	public function it_should_return_foo_using_dispatch_from()
	{
		$this->bus->shouldReceive('handle')->andReturn('foo');

		$collection = new Collection(['data' => 'foo']);

		$this->assertEquals($this->dispatcher->dispatchFrom(TestWithDefaultCommand::class, $collection), 'foo');
	}

	/**
	 * 
	 * @test
	 * 
	 */
	public function it_should_return_foo_using_dispatch_from_array()
	{
		$this->bus->shouldReceive('handle')->andReturn('foo');

		$this->assertEquals($this->dispatcher->dispatchFromArray(TestWithDefaultCommand::class, ['data' => 'foo']), 'foo');
	}

	/**
	 * 
	 * @test
	 * 
	 */
	public function it_should_return_a_command_object_and_the_data_using_marshal_no_extras()
	{
		$collection = new Collection(['data' => 'foo']);
		$command    = $this->dispatcher->marshal(TestCommand::class, $collection);

		$this->assertInstanceOf(TestCommand::class, $command);
		$this->assertEquals($command->data, 'foo');
	}

	/**
	 * 
	 * @test
	 * 
	 */
	public function it_should_return_a_command_object_and_the_data_using_marshal_with_extras()
	{
		$collection = new Collection(['data' => 'foo']);
		$command    = $this->dispatcher->marshal(TestCommand::class, $collection, ['data' => 'bar']);

		$this->assertInstanceOf(TestCommand::class, $command);		
		$this->assertEquals($command->data, 'bar');
		$this->assertNotEquals($command->data, 'foo');
	}

	/**
	 * 
	 * @test
	 * 
	 */
	public function it_should_return_a_command_object_and_the_data_using_marshal_from_array()
	{
		$command    = $this->dispatcher->marshalFromArray(TestCommand::class, ['data' => 'foo']);

		$this->assertInstanceOf(TestCommand::class, $command);
		$this->assertEquals($command->data, 'foo');
	}

	/**
	 * 
	 * @test
	 * 
	 */	
	public function it_should_return_a_command_object_with_data_set_to_null()
	{
		$collection = new Collection(['foo' => 'bar']);
		$command    = $this->dispatcher->marshal(TestWithDefaultCommand::class, $collection);

		$this->assertInstanceOf(TestWithDefaultCommand::class, $command);
		$this->assertNull($command->data);
	}

	/**
	 * 
	 * @test
	 * 
	 */	
	public function it_should_return_a_command_object_with_data_and_disregard_bar()
	{
		$collection = new Collection(['data' => 'foo', 'bar' => 'baz']);
		$command    = $this->dispatcher->marshal(TestCommand::class, $collection);

		$this->assertInstanceOf(TestCommand::class, $command);
		$this->assertEquals($command->data, 'foo');
	}

	/**
	 *
	 * @test
	 * 
	 * @expectedException \VinceRuby\Tactician\Exceptions\MarshalException
	 * 
	 */
	public function it_should_fail_using_marshal()
	{
		$collection = new Collection(['foo' => 'bar']);
		$command    = $this->dispatcher->marshal(TestCommand::class, $collection);
	}
}