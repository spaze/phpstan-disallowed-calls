<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed;

class DisallowedNamespaceFactory
{

	/**
	 * @param array<array{namespace:string, message?:string, allowIn?:string[], allowParamsInAllowed?:array<integer, integer|boolean|string>, allowParamsAnywhere?:array<integer, integer|boolean|string>}> $config
	 * @return DisallowedNamespace[]
	 */
	public function createFromConfig(array $config): array
	{
		$disallowedNamespaces = [];
		foreach ($config as $disallowed) {
			$disallowed = new DisallowedNamespace(
				$disallowed['namespace'],
				$disallowed['message'] ?? null,
				$disallowed['allowIn'] ?? []
			);
			$disallowedNamespaces[$disallowed->getNamespace()] = $disallowed;
		}
		return array_values($disallowedNamespaces);
	}

}
