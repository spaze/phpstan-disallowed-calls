<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed;

use PHPStan\ShouldNotHappenException;
use Spaze\PHPStan\Rules\Disallowed\Params\DisallowedCallParamWithCaseInsensitiveValue;
use Spaze\PHPStan\Rules\Disallowed\Params\DisallowedCallParamWithValue;

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

			$allowParamsInAllowed = $allowParamsAnywhere = $allowExceptParams = [];
			foreach ($disallowedCall['allowParamsInAllowed'] ?? [] as $param => $value) {
				$allowParamsInAllowed[$param] = new DisallowedCallParamWithValue($value);
			}
			foreach ($disallowedCall['allowParamsAnywhere'] ?? [] as $param => $value) {
				$allowParamsAnywhere[$param] = new DisallowedCallParamWithValue($value);
			}
			foreach ($disallowedCall['allowExceptParams'] ?? [] as $param => $value) {
				$allowExceptParams[$param] = new DisallowedCallParamWithValue($value);
			}
			foreach ($disallowedCall['allowExceptCaseInsensitiveParams'] ?? [] as $param => $value) {
				$allowExceptParams[$param] = new DisallowedCallParamWithCaseInsensitiveValue($value);
			}
			$disallowedCall = new DisallowedCall(
				$call,
				$disallowedCall['message'] ?? null,
				$disallowedCall['allowIn'] ?? [],
				$allowParamsInAllowed,
				$allowParamsAnywhere,
				$allowExceptParams
			);
			$calls[$disallowedCall->getKey()] = $disallowedCall;
		}
		return array_values($calls);
	}

}
