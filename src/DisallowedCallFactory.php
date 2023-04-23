<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed;

use PHPStan\ShouldNotHappenException;
use Spaze\PHPStan\Rules\Disallowed\Exceptions\UnsupportedParamTypeInConfigException;
use Spaze\PHPStan\Rules\Disallowed\Formatter\Formatter;
use Spaze\PHPStan\Rules\Disallowed\Params\DisallowedCallParamValue;
use Spaze\PHPStan\Rules\Disallowed\Params\DisallowedCallParamValueAny;
use Spaze\PHPStan\Rules\Disallowed\Params\DisallowedCallParamValueCaseInsensitiveExcept;
use Spaze\PHPStan\Rules\Disallowed\Params\DisallowedCallParamValueExcept;
use Spaze\PHPStan\Rules\Disallowed\Params\DisallowedCallParamValueFlagExcept;
use Spaze\PHPStan\Rules\Disallowed\Params\DisallowedCallParamValueFlagSpecific;
use Spaze\PHPStan\Rules\Disallowed\Params\DisallowedCallParamValueSpecific;

class DisallowedCallFactory
{

	/** @var Formatter */
	private $formatter;


	public function __construct(Formatter $formatter)
	{
		$this->formatter = $formatter;
	}


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
			$calls = (array)$calls;
			try {
				foreach ($calls as $call) {
					$allowInCalls = $allowExceptInCalls = $allowParamsInAllowed = $allowParamsAnywhere = $allowExceptParamsInAllowed = $allowExceptParams = [];
					foreach ($disallowed['allowInFunctions'] ?? $disallowed['allowInMethods'] ?? [] as $allowedCall) {
						$allowInCalls[] = $this->normalizeCall($allowedCall);
					}
					foreach ($disallowed['allowExceptInFunctions'] ?? $disallowed['allowExceptInMethods'] ?? $disallowed['disallowInFunctions'] ?? $disallowed['disallowInMethods'] ?? [] as $disallowedCall) {
						$allowExceptInCalls[] = $this->normalizeCall($disallowedCall);
					}
					foreach ($disallowed['allowParamsInAllowed'] ?? [] as $param => $value) {
						$allowParamsInAllowed[$param] = $this->paramFactory(DisallowedCallParamValueSpecific::class, $param, $value);
					}
					foreach ($disallowed['allowParamsInAllowedAnyValue'] ?? [] as $param => $value) {
						$allowParamsInAllowed[$param] = $this->paramFactory(DisallowedCallParamValueAny::class, $param, $value);
					}
					foreach ($disallowed['allowParamFlagsInAllowed'] ?? [] as $param => $value) {
						$allowParamsInAllowed[$param] = $this->paramFactory(DisallowedCallParamValueFlagSpecific::class, $param, $value);
					}
					foreach ($disallowed['allowParamsAnywhere'] ?? [] as $param => $value) {
						$allowParamsAnywhere[$param] = $this->paramFactory(DisallowedCallParamValueSpecific::class, $param, $value);
					}
					foreach ($disallowed['allowParamsAnywhereAnyValue'] ?? [] as $param => $value) {
						$allowParamsAnywhere[$param] = $this->paramFactory(DisallowedCallParamValueAny::class, $param, $value);
					}
					foreach ($disallowed['allowParamFlagsAnywhere'] ?? [] as $param => $value) {
						$allowParamsAnywhere[$param] = $this->paramFactory(DisallowedCallParamValueFlagSpecific::class, $param, $value);
					}
					foreach ($disallowed['allowExceptParamsInAllowed'] ?? $disallowed['disallowParamsInAllowed'] ?? [] as $param => $value) {
						$allowExceptParamsInAllowed[$param] = $this->paramFactory(DisallowedCallParamValueExcept::class, $param, $value);
					}
					foreach ($disallowed['allowExceptParamFlagsInAllowed'] ?? $disallowed['disallowParamFlagsInAllowed'] ?? [] as $param => $value) {
						$allowExceptParamsInAllowed[$param] = $this->paramFactory(DisallowedCallParamValueFlagExcept::class, $param, $value);
					}
					foreach ($disallowed['allowExceptParams'] ?? $disallowed['disallowParams'] ?? [] as $param => $value) {
						$allowExceptParams[$param] = $this->paramFactory(DisallowedCallParamValueExcept::class, $param, $value);
					}
					foreach ($disallowed['allowExceptParamFlags'] ?? $disallowed['disallowParamFlags'] ?? [] as $param => $value) {
						$allowExceptParams[$param] = $this->paramFactory(DisallowedCallParamValueFlagExcept::class, $param, $value);
					}
					foreach ($disallowed['allowExceptCaseInsensitiveParams'] ?? $disallowed['disallowCaseInsensitiveParams'] ?? [] as $param => $value) {
						$allowExceptParams[$param] = $this->paramFactory(DisallowedCallParamValueCaseInsensitiveExcept::class, $param, $value);
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
			} catch (UnsupportedParamTypeInConfigException $e) {
				throw new ShouldNotHappenException(sprintf('%s: %s', $this->formatter->formatIdentifier($calls), $e->getMessage()));
			}
		}
		return array_values($disallowedCalls);
	}


	private function normalizeCall(string $call): string
	{
		$call = substr($call, -2) === '()' ? substr($call, 0, -2) : $call;
		return ltrim($call, '\\');
	}


	/**
	 * @template T of DisallowedCallParamValue
	 * @param class-string<T> $class
	 * @param int|string $key
	 * @param int|bool|string|null|array{position:int, value?:int|bool|string, name?:string} $value
	 * @return T
	 * @throws UnsupportedParamTypeInConfigException
	 */
	private function paramFactory(string $class, $key, $value): DisallowedCallParamValue
	{
		if (is_numeric($key)) {
			if (is_array($value)) {
				$paramPosition = $value['position'];
				$paramName = $value['name'] ?? null;
				$paramValue = $value['value'] ?? null;
			} elseif ($class === DisallowedCallParamValueAny::class) {
				if (is_numeric($value)) {
					$paramPosition = (int)$value;
					$paramName = null;
				} else {
					$paramPosition = null;
					$paramName = (string)$value;
				}
				$paramValue = null;
			} else {
				$paramPosition = (int)$key;
				$paramName = null;
				$paramValue = $value;
			}
		} else {
			$paramPosition = null;
			$paramName = $key;
			$paramValue = $value;
		}

		if (!is_int($paramValue) && !is_bool($paramValue) && !is_string($paramValue) && !is_null($paramValue)) {
			throw new UnsupportedParamTypeInConfigException($paramPosition, $paramName, gettype($paramValue));
		}
		return new $class($paramPosition, $paramName, $paramValue);
	}

}
