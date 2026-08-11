<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Identifier;

use PHPStan\Reflection\ReflectionProvider;

class Identifier
{

	private ReflectionProvider $reflectionProvider;


	public function __construct(ReflectionProvider $reflectionProvider)
	{
		$this->reflectionProvider = $reflectionProvider;
	}


	/**
	 * @param string $pattern
	 * @param string $value
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
			if (!$this->reflectionProvider->hasClass($value)) {
				return true;
			}
			$attributes = array_map(fn($a) => $a->getName(), $this->reflectionProvider->getClass($value)->getAttributes());
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
