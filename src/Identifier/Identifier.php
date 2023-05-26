<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Identifier;

class Identifier
{

	/**
	 * @param string $pattern
	 * @param string $value
	 * @return bool
	 */
	public function matches(string $pattern, string $value): bool
	{
		$matches = false;
		if ($pattern === $value) {
			$matches = true;
		} elseif (fnmatch($pattern, $value, FNM_NOESCAPE | FNM_CASEFOLD)) {
			$matches = true;
		}
		return $matches;
	}

}
