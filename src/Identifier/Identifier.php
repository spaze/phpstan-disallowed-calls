<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Identifier;

class Identifier
{

	/**
	 * @param string $pattern
	 * @param string|class-string $value
	 * @param list<string> $excludes
	 * @param list<string> $excludeWithAttributes
	 * @return bool
	 */
	public function matches(string $pattern, string $value, array $excludes = [], array $excludeWithAttributes = []): bool
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
		if ($matches && $excludeWithAttributes) {
			$attributes = class_exists($value) || interface_exists($value)
				? array_map(fn ($a) => $a->getName(), (new \ReflectionClass($value))->getAttributes())
				: [];

			foreach ($attributes as $attribute) {
				foreach ($excludeWithAttributes as $excludeWithAttribute) {
					if (fnmatch($excludeWithAttribute, $attribute, FNM_NOESCAPE | FNM_CASEFOLD)) {
						return false;
					}
				}
			}
		}
		return $matches;
	}

}
