<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed;

class DisallowedNamespaceFactory
{

	/**
	 * @param array<array{namespace:string, message?:string, allowIn?:string[], errorIdentifier?:string}> $config
	 * @return DisallowedNamespace[]
	 */
	public function createFromConfig(array $config): array
	{
		$disallowedNamespaces = [];
		foreach ($config as $disallowed) {
			foreach ((array)$disallowed['namespace'] as $namespace) {
				$disallowedNamespace = new DisallowedNamespace(
					$namespace,
					$disallowed['message'] ?? null,
					$disallowed['allowIn'] ?? [],
					$disallowed['errorIdentifier'] ?? ''
				);
				$disallowedNamespaces[$disallowedNamespace->getNamespace()] = $disallowedNamespace;
			}
		}
		return array_values($disallowedNamespaces);
	}

}
