<?php

namespace VinceRuby\Tactician\Tests\Stubs;

class TestWithDefaultCommand
{
	public $data;

	function __construct($data = null)
	{
		$this->data = $data;
	}
}