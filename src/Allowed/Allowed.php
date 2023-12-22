<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Allowed;

use PhpParser\Node\Arg;
use PHPStan\Analyser\Scope;
use PHPStan\PhpDoc\TypeStringResolver;
use PHPStan\Reflection\FunctionReflection;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Type\Constant\ConstantBooleanType;
use PHPStan\Type\Constant\ConstantIntegerType;
use PHPStan\Type\Constant\ConstantStringType;
use PHPStan\Type\NullType;
use PHPStan\Type\Type;
use PHPStan\Type\UnionType;
use Spaze\PHPStan\Rules\Disallowed\DisallowedWithParams;
use Spaze\PHPStan\Rules\Disallowed\Exceptions\UnsupportedParamTypeException;
use Spaze\PHPStan\Rules\Disallowed\Exceptions\UnsupportedParamTypeInConfigException;
use Spaze\PHPStan\Rules\Disallowed\Formatter\Formatter;
use Spaze\PHPStan\Rules\Disallowed\Normalizer\Normalizer;
use Spaze\PHPStan\Rules\Disallowed\Params\Param;
use Spaze\PHPStan\Rules\Disallowed\Params\ParamValue;
use Spaze\PHPStan\Rules\Disallowed\Params\ParamValueAny;
use Spaze\PHPStan\Rules\Disallowed\Params\ParamValueCaseInsensitiveExcept;
use Spaze\PHPStan\Rules\Disallowed\Params\ParamValueExcept;
use Spaze\PHPStan\Rules\Disallowed\Params\ParamValueExceptAny;
use Spaze\PHPStan\Rules\Disallowed\Params\ParamValueFlagExcept;
use Spaze\PHPStan\Rules\Disallowed\Params\ParamValueFlagSpecific;
use Spaze\PHPStan\Rules\Disallowed\Params\ParamValueSpecific;

class Allowed
{

	/** @var Formatter */
	private $formatter;

	/** @var Normalizer */
	private $normalizer;

	/** @var AllowedPath */
	private $allowedPath;

	/** @var TypeStringResolver */
	private $typeStringResolver;


	public function __construct(
		Formatter $formatter,
		Normalizer $normalizer,
		AllowedPath $allowedPath,
		TypeStringResolver $typeStringResolver
	) {
		$this->formatter = $formatter;
		$this->normalizer = $normalizer;
		$this->allowedPath = $allowedPath;
		$this->typeStringResolver = $typeStringResolver;
	}


	/**
	 * @param Scope $scope
	 * @param array<int, Arg>|null $args
	 * @param DisallowedWithParams $disallowed
	 * @return bool
	 */
	public function isAllowed(Scope $scope, ?array $args, DisallowedWithParams $disallowed): bool
	{
		foreach ($disallowed->getAllowInCalls() as $call) {
			if ($this->callMatches($scope, $call)) {
				return $this->hasAllowedParamsInAllowed($scope, $args, $disallowed);
			}
		}
		foreach ($disallowed->getAllowExceptInCalls() as $call) {
			if (!$this->callMatches($scope, $call)) {
				return true;
			}
		}
		foreach ($disallowed->getAllowIn() as $allowedPath) {
			if ($this->allowedPath->matches($scope, $allowedPath)) {
				return $this->hasAllowedParamsInAllowed($scope, $args, $disallowed);
			}
		}
		if ($disallowed->getAllowExceptIn()) {
			foreach ($disallowed->getAllowExceptIn() as $allowedExceptPath) {
				if ($this->allowedPath->matches($scope, $allowedExceptPath)) {
					return false;
				}
			}
			return true;
		}
		if ($disallowed->getAllowExceptParams()) {
			return $this->hasAllowedParams($scope, $args, $disallowed->getAllowExceptParams(), false);
		}
		if ($disallowed->getAllowParamsAnywhere()) {
			return $this->hasAllowedParams($scope, $args, $disallowed->getAllowParamsAnywhere(), true);
		}
		return false;
	}


	private function callMatches(Scope $scope, string $call): bool
	{
		if ($scope->getFunction() instanceof MethodReflection) {
			$name = $this->formatter->getFullyQualified($scope->getFunction()->getDeclaringClass()->getDisplayName(false), $scope->getFunction());
		} elseif ($scope->getFunction() instanceof FunctionReflection) {
			$name = $scope->getFunction()->getName();
		} else {
			$name = '';
		}
		return fnmatch($call, $name, FNM_NOESCAPE | FNM_CASEFOLD);
	}


	/**
	 * @param Scope $scope
	 * @param array<int, Arg>|null $args
	 * @param array<int|string, Param> $allowConfig
	 * @param bool $paramsRequired
	 * @return bool
	 */
	private function hasAllowedParams(Scope $scope, ?array $args, array $allowConfig, bool $paramsRequired): bool
	{
		if ($args === null) {
			return true;
		}

		$disallowedParams = false;
		foreach ($allowConfig as $param) {
			$type = $this->getArgType($args, $scope, $param);
			if ($type === null) {
				return !$paramsRequired;
			}
			if ($type instanceof UnionType) {
				$types = $type->getTypes();
			} else {
				$types = [$type];
			}
			foreach ($types as $type) {
				try {
					$disallowedParams = $disallowedParams || !$param->matches($type);
				} catch (UnsupportedParamTypeException $e) {
					return !$paramsRequired;
				}
			}
		}
		return !$disallowedParams;
	}


	/**
	 * @param Scope $scope
	 * @param array<int, Arg>|null $args
	 * @param DisallowedWithParams $disallowed
	 * @return bool
	 */
	private function hasAllowedParamsInAllowed(Scope $scope, ?array $args, DisallowedWithParams $disallowed): bool
	{
		if ($disallowed->getAllowExceptParamsInAllowed()) {
			return $this->hasAllowedParams($scope, $args, $disallowed->getAllowExceptParamsInAllowed(), false);
		}
		if ($disallowed->getAllowParamsInAllowed()) {
			return $this->hasAllowedParams($scope, $args, $disallowed->getAllowParamsInAllowed(), true);
		}
		return true;
	}


	/**
	 * @param array<int, Arg> $args
	 * @param Scope $scope
	 * @param Param $param
	 * @return Type|null
	 */
	private function getArgType(array $args, Scope $scope, Param $param): ?Type
	{
		foreach ($args as $arg) {
			if ($arg->name && $arg->name->name === $param->getName()) {
				$found = $arg;
				break;
			}
		}
		if (!isset($found)) {
			$found = $args[$param->getPosition() - 1] ?? null;
		}
		return isset($found) ? $scope->getType($found->value) : null;
	}


	/**
	 * @param array $allowed
	 * @phpstan-param AllowDirectivesConfig $allowed
	 * @return AllowedConfig
	 * @throws UnsupportedParamTypeInConfigException
	 */
	public function getConfig(array $allowed): AllowedConfig
	{
		$allowInCalls = $allowExceptInCalls = $allowParamsInAllowed = $allowParamsAnywhere = $allowExceptParamsInAllowed = $allowExceptParams = [];
		foreach ($allowed['allowInFunctions'] ?? $allowed['allowInMethods'] ?? [] as $allowedCall) {
			$allowInCalls[] = $this->normalizer->normalizeCall($allowedCall);
		}
		foreach ($allowed['allowExceptInFunctions'] ?? $allowed['allowExceptInMethods'] ?? $allowed['disallowInFunctions'] ?? $allowed['disallowInMethods'] ?? [] as $disallowedCall) {
			$allowExceptInCalls[] = $this->normalizer->normalizeCall($disallowedCall);
		}
		foreach ($allowed['allowParamsInAllowed'] ?? [] as $param => $value) {
			$allowParamsInAllowed[$param] = $this->paramFactory(ParamValueSpecific::class, $param, $value);
		}
		foreach ($allowed['allowParamsInAllowedAnyValue'] ?? [] as $param => $value) {
			$allowParamsInAllowed[$param] = $this->paramFactory(ParamValueAny::class, $param, $value);
		}
		foreach ($allowed['allowParamFlagsInAllowed'] ?? [] as $param => $value) {
			$allowParamsInAllowed[$param] = $this->paramFactory(ParamValueFlagSpecific::class, $param, $value);
		}
		foreach ($allowed['allowParamsAnywhere'] ?? [] as $param => $value) {
			$allowParamsAnywhere[$param] = $this->paramFactory(ParamValueSpecific::class, $param, $value);
		}
		foreach ($allowed['allowParamsAnywhereAnyValue'] ?? [] as $param => $value) {
			$allowParamsAnywhere[$param] = $this->paramFactory(ParamValueAny::class, $param, $value);
		}
		foreach ($allowed['allowParamFlagsAnywhere'] ?? [] as $param => $value) {
			$allowParamsAnywhere[$param] = $this->paramFactory(ParamValueFlagSpecific::class, $param, $value);
		}
		foreach ($allowed['allowExceptParamsInAllowed'] ?? $allowed['disallowParamsInAllowed'] ?? [] as $param => $value) {
			$allowExceptParamsInAllowed[$param] = $this->paramFactory(ParamValueExcept::class, $param, $value);
		}
		foreach ($allowed['allowExceptParamFlagsInAllowed'] ?? $allowed['disallowParamFlagsInAllowed'] ?? [] as $param => $value) {
			$allowExceptParamsInAllowed[$param] = $this->paramFactory(ParamValueFlagExcept::class, $param, $value);
		}
		foreach ($allowed['allowExceptParams'] ?? $allowed['disallowParams'] ?? [] as $param => $value) {
			$allowExceptParams[$param] = $this->paramFactory(ParamValueExcept::class, $param, $value);
		}
		foreach ($allowed['allowExceptParamsAnyValue'] ?? $allowed['disallowParamsAnyValue'] ?? [] as $param => $value) {
			$allowExceptParams[$param] = $this->paramFactory(ParamValueExceptAny::class, $param, $value);
		}
		foreach ($allowed['allowExceptParamFlags'] ?? $allowed['disallowParamFlags'] ?? [] as $param => $value) {
			$allowExceptParams[$param] = $this->paramFactory(ParamValueFlagExcept::class, $param, $value);
		}
		foreach ($allowed['allowExceptCaseInsensitiveParams'] ?? $allowed['disallowCaseInsensitiveParams'] ?? [] as $param => $value) {
			$allowExceptParams[$param] = $this->paramFactory(ParamValueCaseInsensitiveExcept::class, $param, $value);
		}
		return new AllowedConfig(
			$allowed['allowIn'] ?? [],
			$allowed['allowExceptIn'] ?? $allowed['disallowIn'] ?? [],
			$allowInCalls,
			$allowExceptInCalls,
			$allowParamsInAllowed,
			$allowParamsAnywhere,
			$allowExceptParamsInAllowed,
			$allowExceptParams
		);
	}


	/**
	 * @template T of ParamValue
	 * @param class-string<T> $class
	 * @param int|string $key
	 * @param int|bool|string|null|array{position:int, value?:int|bool|string, typeString?:string, name?:string} $value
	 * @return T
	 * @throws UnsupportedParamTypeInConfigException
	 */
	private function paramFactory(string $class, $key, $value): ParamValue
	{
		if (is_numeric($key)) {
			if (is_array($value)) {
				$paramPosition = $value['position'];
				$paramName = $value['name'] ?? null;
				$paramValue = $value['value'] ?? null;
				$typeString = $value['typeString'] ?? null;
			} elseif (in_array($class, [ParamValueAny::class, ParamValueExceptAny::class], true)) {
				if (is_numeric($value)) {
					$paramPosition = (int)$value;
					$paramName = null;
				} else {
					$paramPosition = null;
					$paramName = (string)$value;
				}
				$paramValue = $typeString = null;
			} else {
				$paramPosition = (int)$key;
				$paramName = null;
				$paramValue = $value;
				$typeString = null;
			}
		} else {
			$paramPosition = null;
			$paramName = $key;
			$paramValue = $value;
			$typeString = null;
		}

		if ($typeString) {
			$type = $this->typeStringResolver->resolve($typeString);
		} elseif (is_int($paramValue)) {
			$type = new ConstantIntegerType($paramValue);
		} elseif (is_bool($paramValue)) {
			$type = new ConstantBooleanType($paramValue);
		} elseif (is_string($paramValue)) {
			$type = new ConstantStringType($paramValue);
		} elseif (is_null($paramValue)) {
			$type = new NullType();
		} else {
			throw new UnsupportedParamTypeInConfigException($paramPosition, $paramName, gettype($paramValue));
		}
		return new $class($paramPosition, $paramName, $type);
	}

}
