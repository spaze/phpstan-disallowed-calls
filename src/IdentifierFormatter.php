<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed;

class IdentifierFormatter
{

	/**
	 * @param list<string> $identifiers
	 * @return string
	 */
	public function format(array $identifiers): string
	{
		return count($identifiers) === 1 ? $identifiers[0] : '{' . implode(',', $identifiers) . '}';
	}

}
