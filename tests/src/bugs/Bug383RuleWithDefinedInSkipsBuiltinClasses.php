<?php
declare(strict_types = 1);

class FooBarFredHelper
{

	public function setWaldo()
	{
	}

}

// defined outside definedIn path, should be allowed
$foo = new FooBarFredHelper();
$foo->setWaldo();

// defined in definedIn path, disallowed
$blade = new \Waldo\Quux\Blade();
$blade->runner();

// built-in classes, definitely not defined in definedIn path, should not be disallowed
$now = new DateTimeImmutable();
$now->format('Y-m-d H:i:s');
