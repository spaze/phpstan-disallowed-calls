<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Identifier;

class Identifier
{

	/**
	 * @param string $pattern
	 * @param string $value
	 * @param list<string> $excludes
	 * @return bool
	 */
	public function matches(string $pattern, string $value, array $excludes): bool
	{
		$matches = false;
		if ($pattern === $value) {
			$matches = true;
		} elseif (fnmatch($pattern, $value, FNM_NOESCAPE | FNM_CASEFOLD)) {
			$matches = true;
		}
		if ($matches) {
			foreach ($excludes as $exclude) {
				if (fnmatch($exclude, $value, FNM_NOESCAPE | FNM_CASEFOLD)) {
					return false;
				}
			}
		}
		return $matches;
	}

}
