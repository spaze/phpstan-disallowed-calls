<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed;

use PHPStan\ShouldNotHappenException;
use Spaze\PHPStan\Rules\Disallowed\Params\DisallowedCallParamExceptCaseInsensitiveValue;
use Spaze\PHPStan\Rules\Disallowed\Params\DisallowedCallParamExceptValue;
use Spaze\PHPStan\Rules\Disallowed\Params\DisallowedCallParamWithAnyValue;
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

			$allowInCalls = $allowParamsInAllowed = $allowParamsAnywhere = $allowExceptParamsInAllowed = $allowExceptParams = [];
			foreach ($disallowedCall['allowInFunctions'] ?? $disallowedCall['allowInMethods'] ?? [] as $allowedCall) {
				$allowInCalls[] = $this->normalizeCall($allowedCall);
			}
			foreach ($disallowedCall['allowParamsInAllowed'] ?? [] as $param => $value) {
				$allowParamsInAllowed[$param] = new DisallowedCallParamWithValue($value);
			}
			foreach ($disallowedCall['allowParamsInAllowedAnyValue'] ?? [] as $param) {
				$allowParamsInAllowed[$param] = new DisallowedCallParamWithAnyValue();
			}
			foreach ($disallowedCall['allowParamsAnywhere'] ?? [] as $param => $value) {
				$allowParamsAnywhere[$param] = new DisallowedCallParamWithValue($value);
			}
			foreach ($disallowedCall['allowParamsAnywhereAnyValue'] ?? [] as $param) {
				$allowParamsAnywhere[$param] = new DisallowedCallParamWithAnyValue();
			}
			foreach ($disallowedCall['allowExceptParamsInAllowed'] ?? [] as $param => $value) {
				$allowExceptParamsInAllowed[$param] = new DisallowedCallParamExceptValue($value);
			}
			foreach ($disallowedCall['allowExceptParams'] ?? [] as $param => $value) {
				$allowExceptParams[$param] = new DisallowedCallParamExceptValue($value);
			}
			foreach ($disallowedCall['allowExceptCaseInsensitiveParams'] ?? [] as $param => $value) {
				$allowExceptParams[$param] = new DisallowedCallParamExceptCaseInsensitiveValue($value);
			}
			$disallowedCall = new DisallowedCall(
				$this->normalizeCall($call),
				$disallowedCall['message'] ?? null,
				$disallowedCall['allowIn'] ?? [],
				$allowInCalls,
				$allowParamsInAllowed,
				$allowParamsAnywhere,
				$allowExceptParamsInAllowed,
				$allowExceptParams
			);
			$calls[$disallowedCall->getKey()] = $disallowedCall;
		}
		return array_values($calls);
	}


	private function normalizeCall(string $call): string
	{
		$call = substr($call, -2) === '()' ? substr($call, 0, -2) : $call;
		return ltrim($call, '\\');
	}

}
