<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed;

class DisallowedNamespaceFactory
{

	/**
	 * @param array<array{namespace:string, message?:string, allowIn?:string[], allowParamsInAllowed?:array<int, int|bool|string>, allowParamsAnywhere?:array<int, int|bool|string>, errorIdentifier?:string}> $config
	 * @return DisallowedNamespace[]
	 */
	public function createFromConfig(array $config): array
	{
		$disallowedNamespaces = [];
		foreach ($config as $disallowed) {
			$disallowed = new DisallowedNamespace(
				$disallowed['namespace'],
				$disallowed['message'] ?? null,
				$disallowed['allowIn'] ?? [],
				$disallowed['errorIdentifier'] ?? ''
			);
			$disallowedNamespaces[$disallowed->getNamespace()] = $disallowed;
		}
		return array_values($disallowedNamespaces);
	}

}
