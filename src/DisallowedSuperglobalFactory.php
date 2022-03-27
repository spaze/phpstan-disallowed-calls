<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed;

use PHPStan\ShouldNotHappenException;

class DisallowedSuperglobalFactory
{

	/**
	 * @param array<array{superglobal?:string, message?:string, allowIn?:string[], errorIdentifier?:string}> $config
	 * @return DisallowedSuperglobal[]
	 * @throws ShouldNotHappenException
	 */
	public function createFromConfig(array $config): array
	{
		$superglobals = [];
		foreach ($config as $disallowedSuperglobal) {
			$superglobal = $disallowedSuperglobal['superglobal'] ?? null;
			if (!$superglobal) {
				throw new ShouldNotHappenException("'superglobal' must be set in configuration items");
			}
			$disallowedSuperglobal = new DisallowedSuperglobal(
				$superglobal,
				$disallowedSuperglobal['message'] ?? null,
				$disallowedSuperglobal['allowIn'] ?? [],
				$disallowedSuperglobal['errorIdentifier'] ?? ''
			);
			$superglobals[$disallowedSuperglobal->getSuperglobal()] = $disallowedSuperglobal;
		}
		return array_values($superglobals);
	}

}
