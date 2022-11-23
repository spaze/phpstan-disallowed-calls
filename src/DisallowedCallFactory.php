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
		$disallowedCalls = [];
		foreach ($config as $disallowed) {
			$calls = $disallowed['function'] ?? $disallowed['method'] ?? null;
			unset($disallowed['function'], $disallowed['method']);
			if (!$calls) {
				throw new ShouldNotHappenException("Either 'method' or 'function' must be set in configuration items");
			}
			foreach ((array)$calls as $call) {
				$allowInCalls = $allowExceptInCalls = $allowParamsInAllowed = $allowParamsAnywhere = $allowExceptParamsInAllowed = $allowExceptParams = [];
				foreach ($disallowed['allowInFunctions'] ?? $disallowed['allowInMethods'] ?? [] as $allowedCall) {
					$allowInCalls[] = $this->normalizeCall($allowedCall);
				}
				foreach ($disallowed['allowExceptInFunctions'] ?? $disallowed['allowExceptInMethods'] ?? $disallowed['disallowInFunctions'] ?? $disallowed['disallowInMethods'] ?? [] as $disallowedCall) {
					$allowExceptInCalls[] = $this->normalizeCall($disallowedCall);
				}
				foreach ($disallowed['allowParamsInAllowed'] ?? [] as $param => $value) {
					$allowParamsInAllowed[$param] = new DisallowedCallParamWithValue($value);
				}
				foreach ($disallowed['allowParamsInAllowedAnyValue'] ?? [] as $param) {
					$allowParamsInAllowed[$param] = new DisallowedCallParamWithAnyValue();
				}
				foreach ($disallowed['allowParamsAnywhere'] ?? [] as $param => $value) {
					$allowParamsAnywhere[$param] = new DisallowedCallParamWithValue($value);
				}
				foreach ($disallowed['allowParamsAnywhereAnyValue'] ?? [] as $param) {
					$allowParamsAnywhere[$param] = new DisallowedCallParamWithAnyValue();
				}
				foreach ($disallowed['allowExceptParamsInAllowed'] ?? $disallowed['disallowParamsInAllowed'] ?? [] as $param => $value) {
					$allowExceptParamsInAllowed[$param] = new DisallowedCallParamExceptValue($value);
				}
				foreach ($disallowed['allowExceptParams'] ?? $disallowed['disallowParams'] ?? [] as $param => $value) {
					$allowExceptParams[$param] = new DisallowedCallParamExceptValue($value);
				}
				foreach ($disallowed['allowExceptCaseInsensitiveParams'] ?? $disallowed['disallowCaseInsensitiveParams'] ?? [] as $param => $value) {
					$allowExceptParams[$param] = new DisallowedCallParamExceptCaseInsensitiveValue($value);
				}
				$disallowedCall = new DisallowedCall(
					$this->normalizeCall($call),
					$disallowed['message'] ?? null,
					$disallowed['allowIn'] ?? [],
					$disallowed['allowExceptIn'] ?? $disallowed['disallowIn'] ?? [],
					$allowInCalls,
					$allowExceptInCalls,
					$allowParamsInAllowed,
					$allowParamsAnywhere,
					$allowExceptParamsInAllowed,
					$allowExceptParams,
					$disallowed['errorIdentifier'] ?? null,
					$disallowed['errorTip'] ?? null
				);
				$disallowedCalls[$disallowedCall->getKey()] = $disallowedCall;
			}
		}
		return array_values($disallowedCalls);
	}


	private function normalizeCall(string $call): string
	{
		$call = substr($call, -2) === '()' ? substr($call, 0, -2) : $call;
		return ltrim($call, '\\');
	}

}
