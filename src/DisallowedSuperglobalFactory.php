<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed;

use PHPStan\ShouldNotHappenException;

class DisallowedSuperglobalFactory implements DisallowedVariableFactory
{

	/**
	 * @param array<array{superglobal?:string, message?:string, allowIn?:string[], errorIdentifier?:string}> $config
	 * @return DisallowedVariable[]
	 * @throws ShouldNotHappenException
	 */
	public function getDisallowedVariables(array $config): array
	{
		$superglobals = [];
		foreach ($config as $disallowedSuperglobal) {
			$superglobal = $disallowedSuperglobal['superglobal'] ?? null;
			if (!$superglobal) {
				throw new ShouldNotHappenException("'superglobal' must be set in configuration items");
			}
			$disallowedSuperglobal = new DisallowedVariable(
				$superglobal,
				$disallowedSuperglobal['message'] ?? null,
				$disallowedSuperglobal['allowIn'] ?? [],
				$disallowedSuperglobal['errorIdentifier'] ?? ''
			);
			$superglobals[$disallowedSuperglobal->getVariable()] = $disallowedSuperglobal;
		}
		return array_values($superglobals);
	}

}
