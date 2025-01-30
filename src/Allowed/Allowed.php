<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Allowed;

use PhpParser\Node\Arg;
use PHPStan\Analyser\Scope;
use PHPStan\BetterReflection\Reflection\Adapter\FakeReflectionAttribute;
use PHPStan\BetterReflection\Reflection\Adapter\ReflectionAttribute;
use PHPStan\Reflection\FunctionReflection;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Type\Type;
use PHPStan\Type\UnionType;
use Spaze\PHPStan\Rules\Disallowed\Disallowed;
use Spaze\PHPStan\Rules\Disallowed\DisallowedWithParams;
use Spaze\PHPStan\Rules\Disallowed\Exceptions\UnsupportedParamTypeException;
use Spaze\PHPStan\Rules\Disallowed\Formatter\Formatter;
use Spaze\PHPStan\Rules\Disallowed\Params\Param;

class Allowed
{

	private Formatter $formatter;

	private AllowedPath $allowedPath;


	public function __construct(
		Formatter $formatter,
		AllowedPath $allowedPath
	) {
		$this->formatter = $formatter;
		$this->allowedPath = $allowedPath;
	}


	/**
	 * @param Scope $scope
	 * @param array<Arg>|null $args
	 * @param Disallowed|DisallowedWithParams $disallowed
	 * @return bool
	 */
	public function isAllowed(Scope $scope, ?array $args, Disallowed $disallowed): bool
	{
		$hasParams = $disallowed instanceof DisallowedWithParams;
		foreach ($disallowed->getAllowInCalls() as $call) {
			if ($this->callMatches($scope, $call)) {
				return !$hasParams || $this->hasAllowedParamsInAllowed($scope, $args, $disallowed);
			}
		}
		foreach ($disallowed->getAllowExceptInCalls() as $call) {
			if (!$this->callMatches($scope, $call)) {
				return true;
			}
		}
		foreach ($disallowed->getAllowIn() as $allowedPath) {
			if ($this->allowedPath->matches($scope, $allowedPath)) {
				return !$hasParams || $this->hasAllowedParamsInAllowed($scope, $args, $disallowed);
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
		if ($hasParams && $disallowed->getAllowExceptParams()) {
			return $this->hasAllowedParams($scope, $args, $disallowed->getAllowExceptParams(), false);
		}
		if ($hasParams && $disallowed->getAllowParamsAnywhere()) {
			return $this->hasAllowedParams($scope, $args, $disallowed->getAllowParamsAnywhere(), true);
		}
		if ($disallowed->getAllowInClassWithAttributes() && $scope->isInClass()) {
			return $this->hasAllowedAttribute(
				$scope->getClassReflection()->getNativeReflection()->getAttributes(),
				$disallowed->getAllowInClassWithAttributes(),
			);
		}
		if ($disallowed->getAllowExceptInClassWithAttributes() && $scope->isInClass()) {
			return !$this->hasAllowedAttribute(
				$scope->getClassReflection()->getNativeReflection()->getAttributes(),
				$disallowed->getAllowExceptInClassWithAttributes(),
			);
		}
		if ($disallowed->getAllowInClassWithMethodAttributes() && $scope->isInClass()) {
			$attributes = [];
			foreach ($scope->getClassReflection()->getNativeReflection()->getMethods() as $method) {
				$attributes = array_merge($attributes, $method->getAttributes());
			}
			return $this->hasAllowedAttribute($attributes, $disallowed->getAllowInClassWithMethodAttributes());
		}
		if ($disallowed->getAllowExceptInClassWithMethodAttributes() && $scope->isInClass()) {
			$attributes = [];
			foreach ($scope->getClassReflection()->getNativeReflection()->getMethods() as $method) {
				$attributes = array_merge($attributes, $method->getAttributes());
			}
			return !$this->hasAllowedAttribute($attributes, $disallowed->getAllowExceptInClassWithMethodAttributes());
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
	 * @param array<Arg>|null $args
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
	 * @param array<Arg>|null $args
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
	 * @param list<ReflectionAttribute|FakeReflectionAttribute> $attributes
	 * @param list<string> $allowConfig
	 * @return bool
	 */
	private function hasAllowedAttribute(array $attributes, array $allowConfig): bool
	{
		$names = [];
		foreach ($attributes as $attribute) {
			$names[] = $attribute->getName();
		}
		foreach ($allowConfig as $allowAttribute) {
			foreach ($names as $name) {
				if (fnmatch($allowAttribute, $name, FNM_NOESCAPE | FNM_CASEFOLD)) {
					return true;
				}
			}
		}
		return false;
	}


	/**
	 * @param array<Arg> $args
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
		return isset($found, $found->value) ? $scope->getType($found->value) : null;
	}

}
