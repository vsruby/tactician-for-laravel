<?php

namespace VinceRuby\Tactician\Tests\Stubs;

use VinceRuby\Tactician\Tests\Stubs\TestCommand;

class TestCommandHandler
{
	public function handle(TestCommand $command)
	{
		return true;
	}
}