<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Allowed;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Function_;
use PHPStan\Analyser\Scope;
use PHPStan\BetterReflection\Reflector\Reflector;
use PHPStan\Reflection\FunctionReflection;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Type\Type;
use PHPStan\Type\UnionType;
use Spaze\PHPStan\Rules\Disallowed\Disallowed;
use Spaze\PHPStan\Rules\Disallowed\DisallowedWithParams;
use Spaze\PHPStan\Rules\Disallowed\DisallowedWithTypeHints;
use Spaze\PHPStan\Rules\Disallowed\Exceptions\UnsupportedParamTypeException;
use Spaze\PHPStan\Rules\Disallowed\Formatter\Formatter;
use Spaze\PHPStan\Rules\Disallowed\Identifier\Identifier;
use Spaze\PHPStan\Rules\Disallowed\Params\Param;
use Spaze\PHPStan\Rules\Disallowed\Type\TypeHintContextVisitor;

class Allowed
{

	private Formatter $formatter;

	private Reflector $reflector;

	private Identifier $identifier;

	private AllowedPath $allowedPath;


	public function __construct(
		Formatter $formatter,
		Reflector $reflector,
		Identifier $identifier,
		AllowedPath $allowedPath
	) {
		$this->formatter = $formatter;
		$this->reflector = $reflector;
		$this->identifier = $identifier;
		$this->allowedPath = $allowedPath;
	}


	/**
	 * @param Node|null $node
	 * @param Scope $scope
	 * @param array<Arg>|null $args
	 * @param Disallowed|DisallowedWithParams $disallowed
	 * @param UsagePosition::*|null $position
	 * @return bool
	 */
	public function isAllowed(?Node $node, Scope $scope, ?array $args, Disallowed $disallowed, ?int $position = null): bool
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
		if ($disallowed instanceof DisallowedWithTypeHints) {
			if ($position !== null) {
				if ($disallowed->getAllowInPosition($position)) {
					return true;
				}
				if ($disallowed->getAllowExceptInPosition($position)) {
					return false;
				}
			}
			foreach ([UsagePosition::PARAM_TYPE, UsagePosition::RETURN_TYPE] as $case) {
				if ($disallowed->getAllowExceptInPosition($case)) {
					return true;
				}
			}
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
	 * @param list<string> $attributeNames
	 * @param list<string> $allowConfig
	 * @return bool
	 */
	private function hasAllowedAttribute(array $attributeNames, array $allowConfig): bool
	{
		foreach ($allowConfig as $allowAttribute) {
			foreach ($attributeNames as $name) {
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
	 * @return list<string>
	 */
	private function getAttributes(Scope $scope): array
	{
		if (!$scope->isInClass()) {
			return [];
		}
		return array_map(static fn($a) => $a->getName(), $scope->getClassReflection()->getNativeReflection()->getAttributes());
	}


	/**
	 * @param Node|null $node
	 * @param Scope $scope
	 * @return list<string>
	 */
	private function getCallAttributes(?Node $node, Scope $scope): array
	{
		$function = $scope->getFunction();
		if ($function instanceof MethodReflection) {
			if (!$scope->isInClass()) {
				return [];
			}
			return array_map(static fn($a) => $a->getName(), $scope->getClassReflection()->getNativeReflection()->getMethod($function->getName())->getAttributes());
		} elseif ($function instanceof FunctionReflection) {
			return array_map(static fn($a) => $a->getName(), $this->reflector->reflectFunction($function->getName())->getAttributes());
		} elseif ($function === null) {
			if ($node instanceof ClassMethod && $scope->isInClass()) {
				return array_map(static fn($a) => $a->getName(), $scope->getClassReflection()->getNativeReflection()->getMethod($node->name->name)->getAttributes());
			} elseif ($node instanceof Function_) {
				return array_map(static fn($a) => $a->getName(), $this->reflector->reflectFunction(($node->namespacedName ?? $node->name)->toString())->getAttributes());
			}
			// $scope->getFunction() is null in param/return type hints, use the enclosing function attributes stored by TypeHintContextVisitor
			if ($node !== null) {
				$names = $node->getAttribute(TypeHintContextVisitor::ATTRIBUTE_ENCLOSING_FUNCTION_ATTR_NAMES);
				if (is_array($names)) {
					$result = [];
					foreach ($names as $name) {
						if (is_string($name)) {
							$result[] = $name;
						}
					}
					return $result;
				}
			}
		}
		return [];
	}


	/**
	 * @param Scope $scope
	 * @return list<string>
	 */
	private function getAllMethodAttributes(Scope $scope): array
	{
		if (!$scope->isInClass()) {
			return [];
		}
		$names = [];
		foreach ($scope->getClassReflection()->getNativeReflection()->getMethods() as $method) {
			foreach ($method->getAttributes() as $attribute) {
				$names[] = $attribute->getName();
			}
		}
		return $names;
	}

}
