<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Identifier;

class IdentifierWithAttribute
{

	/**
	 * @param string $pattern
	 * @param string $value
	 * @param list<string> $excludes
	 * @param list<string> $excludeWithAttributes
	 * @return bool
	 */
	public function matches(string $pattern, string $value, array $excludes = []): bool
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
            $attributes = array_map(fn ($a) => $a->getName(), (new \ReflectionClass($value))->getAttributes());
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

	public function andDoesntHaveAttribute(string $attribute): bool
	{
		$attributes = array_map(fn ($a) => $a->getName(), (new \ReflectionClass($value))->getAttributes());
		foreach ($attributes as $attribute) {
			if (fnmatch($attribute, $value, FNM_NOESCAPE | FNM_CASEFOLD)) {
				return false;
			}
		}
		return true;
	}
}
