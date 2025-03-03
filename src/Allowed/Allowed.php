<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Allowed;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Function_;
use PHPStan\Analyser\Scope;
use PHPStan\BetterReflection\Reflection\Adapter\FakeReflectionAttribute;
use PHPStan\BetterReflection\Reflection\Adapter\ReflectionAttribute;
use PHPStan\BetterReflection\Reflection\ReflectionAttribute as BetterReflectionAttribute;
use PHPStan\BetterReflection\Reflector\Reflector;
use PHPStan\Reflection\FunctionReflection;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Type\Type;
use PHPStan\Type\UnionType;
use Spaze\PHPStan\Rules\Disallowed\Disallowed;
use Spaze\PHPStan\Rules\Disallowed\DisallowedWithParams;
use Spaze\PHPStan\Rules\Disallowed\Exceptions\UnsupportedParamTypeException;
use Spaze\PHPStan\Rules\Disallowed\Formatter\Formatter;
use Spaze\PHPStan\Rules\Disallowed\Identifier\Identifier;
use Spaze\PHPStan\Rules\Disallowed\Params\Param;

class Allowed
{

	private Formatter $formatter;

	private Reflector $reflector;

	private Identifier $identifier;

	private GetAttributesWhenInSignature $attributesWhenInSignature;

	private AllowedPath $allowedPath;


	public function __construct(
		Formatter $formatter,
		Reflector $reflector,
		Identifier $identifier,
		GetAttributesWhenInSignature $attributesWhenInSignature,
		AllowedPath $allowedPath
	) {
		$this->formatter = $formatter;
		$this->reflector = $reflector;
		$this->identifier = $identifier;
		$this->attributesWhenInSignature = $attributesWhenInSignature;
		$this->allowedPath = $allowedPath;
	}


	/**
	 * @param Node|null $node
	 * @param Scope $scope
	 * @param array<Arg>|null $args
	 * @param Disallowed|DisallowedWithParams $disallowed
	 * @return bool
	 */
	public function isAllowed(?Node $node, Scope $scope, ?array $args, Disallowed $disallowed): bool
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
		if ($disallowed->getAllowInInstanceOf()) {
			return $this->isInstanceOf($scope, $disallowed->getAllowInInstanceOf());
		}
		if ($disallowed->getAllowExceptInInstanceOf()) {
			return !$this->isInstanceOf($scope, $disallowed->getAllowExceptInInstanceOf());
		}
		if ($hasParams && $disallowed->getAllowExceptParams()) {
			return $this->hasAllowedParams($scope, $args, $disallowed->getAllowExceptParams(), false);
		}
		if ($hasParams && $disallowed->getAllowParamsAnywhere()) {
			return $this->hasAllowedParams($scope, $args, $disallowed->getAllowParamsAnywhere(), true);
		}
		if ($disallowed->getAllowInClassWithAttributes()) {
			return $this->hasAllowedAttribute($this->getAttributes($scope), $disallowed->getAllowInClassWithAttributes());
		}
		if ($disallowed->getAllowExceptInClassWithAttributes()) {
			return !$this->hasAllowedAttribute($this->getAttributes($scope), $disallowed->getAllowExceptInClassWithAttributes());
		}
		if ($disallowed->getAllowInCallsWithAttributes()) {
			return $this->hasAllowedAttribute($this->getCallAttributes($node, $scope), $disallowed->getAllowInCallsWithAttributes());
		}
		if ($disallowed->getAllowExceptInCallsWithAttributes()) {
			return !$this->hasAllowedAttribute($this->getCallAttributes($node, $scope), $disallowed->getAllowExceptInCallsWithAttributes());
		}
		if ($disallowed->getAllowInClassWithMethodAttributes()) {
			return $this->hasAllowedAttribute($this->getAllMethodAttributes($scope), $disallowed->getAllowInClassWithMethodAttributes());
		}
		if ($disallowed->getAllowExceptInClassWithMethodAttributes()) {
			return !$this->hasAllowedAttribute($this->getAllMethodAttributes($scope), $disallowed->getAllowExceptInClassWithMethodAttributes());
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
		return $this->identifier->matches($call, $name);
	}


	/**
	 * @param Scope $scope
	 * @param list<string> $allowConfig
	 * @return bool
	 */
	private function isInstanceOf(Scope $scope, array $allowConfig): bool
	{
		foreach ($allowConfig as $allowInstanceOf) {
			if ($scope->isInClass() && $scope->getClassReflection()->is($allowInstanceOf)) {
				return true;
			}
		}
		return false;
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
	 * @param list<FakeReflectionAttribute|ReflectionAttribute|BetterReflectionAttribute> $attributes
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
				if ($this->identifier->matches($allowAttribute, $name)) {
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


	/**
	 * @param Scope $scope
	 * @return list<FakeReflectionAttribute>|list<ReflectionAttribute>
	 */
	private function getAttributes(Scope $scope): array
	{
		return $scope->isInClass() ? $scope->getClassReflection()->getNativeReflection()->getAttributes() : [];
	}


	/**
	 * @param Node|null $node
	 * @param Scope $scope
	 * @return list<FakeReflectionAttribute|ReflectionAttribute|BetterReflectionAttribute>
	 */
	private function getCallAttributes(?Node $node, Scope $scope): array
	{
		$function = $scope->getFunction();
		if ($function instanceof MethodReflection) {
			return $scope->isInClass() ? $scope->getClassReflection()->getNativeReflection()->getMethod($function->getName())->getAttributes() : [];
		} elseif ($function instanceof FunctionReflection) {
			return $this->reflector->reflectFunction($function->getName())->getAttributes();
		} elseif ($function === null) {
			if ($node instanceof ClassMethod && $scope->isInClass()) {
				return $scope->getClassReflection()->getNativeReflection()->getMethod($node->name->name)->getAttributes();
			} elseif ($node instanceof Function_) {
				return $this->reflector->reflectFunction($node->name->name)->getAttributes();
			}
			$attributes = $this->attributesWhenInSignature->get($scope);
			if ($attributes !== null) {
				return $attributes;
			}
		}
		return [];
	}


	/**
	 * @param Scope $scope
	 * @return list<FakeReflectionAttribute>|list<ReflectionAttribute>
	 */
	private function getAllMethodAttributes(Scope $scope): array
	{
		if (!$scope->isInClass()) {
			return [];
		}
		$attributes = [];
		foreach ($scope->getClassReflection()->getNativeReflection()->getMethods() as $method) {
			$methodAttributes = $method->getAttributes();
			if ($methodAttributes !== []) {
				$attributes = array_merge($attributes, $methodAttributes);
			}
		}
		return $attributes;
	}

}
