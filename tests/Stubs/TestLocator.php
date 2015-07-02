<?php

namespace VinceRuby\Tactician\Tests\Stubs;

use VinceRuby\Tactician\Locator;

class TestLocator extends Locator
{
	public function __get($key)
	{
		return $this->$key;
	}
}