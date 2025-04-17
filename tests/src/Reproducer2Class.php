<?php
declare(strict_types = 1);

namespace Reproducer2;

class Reproducer2Class
{
	public function __construct()
	{
	}

	public function foo(\DateTime $d): void
	{
	}

	public function bar(\Baz\Waldo $d): void
	{
	}
}
