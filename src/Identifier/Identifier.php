<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Identifier;

class Identifier
{

	/**
	 * @param string $pattern
	 * @param string $value
	 * @param list<string> $excludes
	 * @param list<string> $excludeClassesWithAttribute
	 * @return bool
	 */
	public function matches(string $pattern, string $value, array $excludes = [], array $excludeClassesWithAttribute = []): bool
	{
		$matches = false;
		if ($pattern === $value) {
			$matches = true;
		} elseif (fnmatch($pattern, $value, FNM_NOESCAPE | FNM_CASEFOLD)) {
			$matches = true;
		}
		if ($matches && $excludes) {
			foreach ($excludes as $exclude) {
				if (fnmatch($exclude, $value, FNM_NOESCAPE | FNM_CASEFOLD)) {
					return false;
				}
			}
		}
		if ($matches && $excludeClassesWithAttribute) {
            $attributes = array_map(fn ($a) => $a->getName(), (new \ReflectionClass($value))->getAttributes());
            if (array_intersect($excludeClassesWithAttribute, $attributes)) {
                return false;
            }
        }
		return $matches;
	}

}
