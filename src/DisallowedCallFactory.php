<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed;

use PHPStan\ShouldNotHappenException;

class DisallowedCallFactory
{

	/**
	 * @param array $config
	 * @phpstan-param ForbiddenCallsConfig $config
	 * @noinspection PhpUndefinedClassInspection ForbiddenCallsConfig is a type alias defined in PHPStan config
	 * @return DisallowedCall[]
	 * @throws ShouldNotHappenException
	 */
	public function createFromConfig(array $config): array
	{
		$calls = [];
		foreach ($config as $disallowedCall) {
			$call = $disallowedCall['function'] ?? $disallowedCall['method'] ?? null;
			if (!$call) {
				throw new ShouldNotHappenException("Either 'method' or 'function' must be set in configuration items");
			}
			$disallowedCall = new DisallowedCall(
				$call,
				$disallowedCall['message'] ?? null,
				$disallowedCall['allowIn'] ?? [],
				$disallowedCall['allowParamsInAllowed'] ?? [],
				$disallowedCall['allowParamsAnywhere'] ?? [],
				$disallowedCall['allowExceptParams'] ?? [],
				$disallowedCall['allowExceptCaseInsensitiveParams'] ?? []
			);
			$calls[$disallowedCall->getKey()] = $disallowedCall;
		}
		return array_values($calls);
	}

}
