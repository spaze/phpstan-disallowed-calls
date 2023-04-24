<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Formatter;

use PHPStan\Reflection\MethodReflection;

class Formatter
{

	public function getFullyQualified(string $class, MethodReflection $method): string
	{
		return sprintf('%s::%s', $class, $method->getName());
	}


	/**
	 * @param list<string> $identifiers
	 * @return string
	 */
	public function formatIdentifier(array $identifiers): string
	{
		return count($identifiers) === 1 ? $identifiers[0] : '{' . implode(',', $identifiers) . '}';
	}

}
