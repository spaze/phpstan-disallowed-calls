<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed;

use PHPStan\ShouldNotHappenException;

class DisallowedNamespaceFactory
{

	/**
	 * @param array<array{namespace?:string, class?:string, message?:string, allowIn?:string[], allowExceptIn?:string[], disallowIn?:string[], errorIdentifier?:string, errorTip?:string}> $config
	 * @return DisallowedNamespace[]
	 */
	public function createFromConfig(array $config): array
	{
		$disallowedNamespaces = [];
		foreach ($config as $disallowed) {
			$namespaces = $disallowed['namespace'] ?? $disallowed['class'] ?? null;
			unset($disallowed['namespace'], $disallowed['class']);
			if (!$namespaces) {
				throw new ShouldNotHappenException("Either 'namespace' or 'class' must be set in configuration items");
			}
			foreach ((array)$namespaces as $namespace) {
				$disallowedNamespace = new DisallowedNamespace(
					$namespace,
					$disallowed['message'] ?? null,
					$disallowed['allowIn'] ?? [],
					$disallowed['allowExceptIn'] ?? $disallowed['disallowIn'] ?? [],
					$disallowed['errorIdentifier'] ?? null,
					$disallowed['errorTip'] ?? null
				);
				$disallowedNamespaces[$disallowedNamespace->getNamespace()] = $disallowedNamespace;
			}
		}
		return array_values($disallowedNamespaces);
	}

}
