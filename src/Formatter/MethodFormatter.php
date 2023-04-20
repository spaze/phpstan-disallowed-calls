<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Formatter;

use PHPStan\Reflection\MethodReflection;

class MethodFormatter
{

	public function getFullyQualified(string $class, MethodReflection $method): string
	{
		return sprintf('%s::%s', $class, $method->getName());
	}

}
