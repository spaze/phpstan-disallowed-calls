<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed;

use PHPStan\ShouldNotHappenException;

class DisallowedSuperglobalFactory implements DisallowedVariableFactory
{

	/**
	 * @see https://www.php.net/variables.superglobals
	 */
	private const SUPERGLOBALS = [
		'$GLOBALS',
		'$_SERVER',
		'$_GET',
		'$_POST',
		'$_FILES',
		'$_COOKIE',
		'$_SESSION',
		'$_REQUEST',
		'$_ENV',
	];


	/**
	 * @param array<array{superglobal?:string|list<string>, message?:string, allowIn?:list<string>, allowExceptIn?:list<string>, disallowIn?:list<string>, errorIdentifier?:string, errorTip?:string}> $config
	 * @return list<DisallowedVariable>
	 * @throws ShouldNotHappenException
	 */
	public function getDisallowedVariables(array $config): array
	{
		$disallowedSuperglobals = [];
		foreach ($config as $disallowed) {
			$superglobals = $disallowed['superglobal'] ?? null;
			unset($disallowed['superglobal']);
			if (!$superglobals) {
				throw new ShouldNotHappenException("'superglobal' must be set in configuration items");
			}
			foreach ((array)$superglobals as $superglobal) {
				if (!in_array($superglobal, self::SUPERGLOBALS, true)) {
					throw new ShouldNotHappenException("{$superglobal} is not a superglobal variable");
				}
				$disallowedSuperglobal = new DisallowedVariable(
					$superglobal,
					$disallowed['message'] ?? null,
					$disallowed['allowIn'] ?? [],
					$disallowed['allowExceptIn'] ?? $disallowed['disallowIn'] ?? [],
					$disallowed['errorIdentifier'] ?? null,
					$disallowed['errorTip'] ?? null
				);
				$disallowedSuperglobals[$disallowedSuperglobal->getVariable()] = $disallowedSuperglobal;
			}
		}
		return array_values($disallowedSuperglobals);
	}

}
