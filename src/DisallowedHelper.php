<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PHPStan\Analyser\Scope;
use PHPStan\Broker\ClassNotFoundException;
use PHPStan\Reflection\MethodReflection;
use PHPStan\ShouldNotHappenException;
use PHPStan\Type\Constant\ConstantStringType;
use PHPStan\Type\ConstantScalarType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;
use PHPStan\Type\TypeWithClassName;

class DisallowedHelper
{

	/** @var FileHelper */
	private $fileHelper;


	public function __construct(FileHelper $fileHelper)
	{
		$this->fileHelper = $fileHelper;
	}


	/**
	 * @param Scope $scope
	 * @param Expr|null $node
	 * @phpstan-param ForbiddenCalls|null $node
	 * @noinspection PhpUndefinedClassInspection ForbiddenCalls is a type alias defined in PHPStan config
	 * @param DisallowedCall $disallowedCall
	 * @return boolean
	 */
	private function isAllowed(Scope $scope, ?Node $node, DisallowedCall $disallowedCall): bool
	{
		$hasAllowParamsAnywhere = $disallowedCall->getAllowParamsAnywhere() && $this->hasAllowedParams($scope, $node, $disallowedCall->getAllowParamsAnywhere());
		foreach ($disallowedCall->getAllowIn() as $allowedPath) {
			if (fnmatch($this->fileHelper->absolutizePath($allowedPath), $scope->getFile())) {
				if ($disallowedCall->getAllowParamsInAllowed()) {
					return $hasAllowParamsAnywhere || $this->hasAllowedParams($scope, $node, $disallowedCall->getAllowParamsInAllowed());
				}
				return true;
			}
		}
		if ($disallowedCall->getAllowParamsAnywhere()) {
			return $hasAllowParamsAnywhere;
		}
		return false;
	}


	/**
	 * @param Scope $scope
	 * @param Expr|null $node
	 * @phpstan-param ForbiddenCalls|null $node
	 * @noinspection PhpUndefinedClassInspection ForbiddenCalls is a type alias defined in PHPStan config
	 * @param array<integer, integer|boolean|string> $allowConfig
	 * @return boolean
	 */
	private function hasAllowedParams(Scope $scope, ?Node $node, array $allowConfig): bool
	{
		if (!$node) {
			return true;
		}

		foreach ($allowConfig as $param => $value) {
			$type = $this->getArgType($node, $scope, $param);
			if (!$type instanceof ConstantScalarType || $value !== $type->getValue()) {
				return false;
			}
		}
		return true;
	}


	/**
	 * @param Scope $scope
	 * @param Expr|null $node
	 * @phpstan-param ForbiddenCalls|null $node
	 * @noinspection PhpUndefinedClassInspection ForbiddenCalls is a type alias defined in PHPStan config
	 * @param DisallowedCall $disallowedCall
	 * @return boolean
	 */
	private function matchesAllowExceptParam(Scope $scope, ?Node $node, DisallowedCall $disallowedCall): bool
	{
		if (!$node) {
			return false;
		}

		foreach ($disallowedCall->getAllowExceptParams() as $param => $value) {
			$type = $this->getArgType($node, $scope, $param);
			if ($type instanceof ConstantScalarType && $value === $type->getValue()) {
				return true;
			}
		}
		foreach ($disallowedCall->getAllowExceptCaseInsensitiveParams() as $param => $value) {
			$type = $this->getArgType($node, $scope, $param);
			if ($type instanceof ConstantScalarType) {
				$a = is_string($value) ? strtolower($value) : $value;
				$b = $type instanceof ConstantStringType ? strtolower($type->getValue()) : $type->getValue();
				if ($a === $b) {
					return true;
				}
			}
		}
		return false;
	}


	/**
	 * @param Expr $node
	 * @phpstan-param ForbiddenCalls $node
	 * @noinspection PhpUndefinedClassInspection ForbiddenCalls is a type alias defined in PHPStan config
	 * @param Scope $scope
	 * @param int $param
	 * @return Type|null
	 */
	private function getArgType(Node $node, Scope $scope, int $param): ?Type
	{
		$arg = $node->args[$param - 1] ?? null;
		return $arg ? $scope->getType($arg->value) : null;
	}


	/**
	 * @param array $config
	 * @phpstan-param ForbiddenCallsConfig $config
	 * @noinspection PhpUndefinedClassInspection ForbiddenCallsConfig is a type alias defined in PHPStan config
	 * @return DisallowedCall[]
	 * @throws ShouldNotHappenException
	 */
	public function createCallsFromConfig(array $config): array
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


	/**
	 * @param array<array{class?:string, constant?:string, message?:string, allowIn?:string[]}> $config
	 * @return DisallowedConstant[]
	 * @throws ShouldNotHappenException
	 */
	public function createConstantsFromConfig(array $config): array
	{
		$constants = [];
		foreach ($config as $disallowedConstant) {
			$constant = $disallowedConstant['constant'] ?? null;
			if (!$constant) {
				throw new ShouldNotHappenException("'constant' must be set in configuration items");
			}
			$class = $disallowedConstant['class'] ?? null;
			$disallowedConstant = new DisallowedConstant(
				$class ? "{$class}::{$constant}" : $constant,
				$disallowedConstant['message'] ?? null,
				$disallowedConstant['allowIn'] ?? []
			);
			$constants[$disallowedConstant->getConstant()] = $disallowedConstant;
		}
		return array_values($constants);
	}


	/**
	 * @param Expr|null $node
	 * @phpstan-param ForbiddenCalls|null $node
	 * @noinspection PhpUndefinedClassInspection ForbiddenCalls is a type alias defined in PHPStan config
	 * @param Scope $scope
	 * @param string $name
	 * @param string|null $displayName
	 * @param DisallowedCall[] $disallowedCalls
	 * @param string|null $message
	 * @return string[]
	 */
	public function getDisallowedMessage(?Node $node, Scope $scope, string $name, ?string $displayName, array $disallowedCalls, ?string $message = null): array
	{
		foreach ($disallowedCalls as $disallowedCall) {
			if ($this->callMatches($scope, $node, $disallowedCall, $name) && !$this->isAllowed($scope, $node, $disallowedCall)) {
				return [
					sprintf(
						$message ?? 'Calling %s is forbidden, %s%s',
						($displayName && $displayName !== $name) ? "{$name}() (as {$displayName}())" : "{$name}()",
						$disallowedCall->getMessage(),
						$disallowedCall->getCall() !== $name ? " [{$name}() matches {$disallowedCall->getCall()}()]" : ''
					),
				];
			}
		}
		return [];
	}


	/**
	 * @param Scope $scope
	 * @param Expr|null $node
	 * @phpstan-param ForbiddenCalls|null $node
	 * @noinspection PhpUndefinedClassInspection ForbiddenCalls is a type alias defined in PHPStan config
	 * @param DisallowedCall $disallowedCall
	 * @param string $name
	 * @return bool
	 */
	private function callMatches(Scope $scope, ?Node $node, DisallowedCall $disallowedCall, string $name): bool
	{
		if ($name === $disallowedCall->getCall() || fnmatch($disallowedCall->getCall(), $name, FNM_NOESCAPE)) {
			$noAllowExceptParams = count($disallowedCall->getAllowExceptParams()) === 0 && count($disallowedCall->getAllowExceptCaseInsensitiveParams()) === 0;
			return $noAllowExceptParams || $this->matchesAllowExceptParam($scope, $node, $disallowedCall);
		}
		return false;
	}


	/**
	 * @param Name|Expr $class
	 * @param Node $node
	 * @param Scope $scope
	 * @param DisallowedCall[] $disallowedCalls
	 * @return string[]
	 * @throws ClassNotFoundException
	 */
	public function getDisallowedMethodMessage($class, Node $node, Scope $scope, array $disallowedCalls): array
	{
		/** @var MethodCall|StaticCall $node */
		if (!($node->name instanceof Identifier)) {
			return [];
		}

		$calledOnType = $this->resolveType($class, $scope);
		if ($calledOnType->canCallMethods()->yes() && $calledOnType->hasMethod($node->name->name)->yes()) {
			$method = $calledOnType->getMethod($node->name->name, $scope);
			$calledAs = ($calledOnType instanceof TypeWithClassName ? $this->getFullyQualified($calledOnType->getClassName(), $method) : null);

			foreach ($method->getDeclaringClass()->getTraits() as $trait) {
				if ($trait->hasMethod($method->getName())) {
					$declaredAs = $this->getFullyQualified($trait->getDisplayName(), $method);
					$message = $this->getDisallowedMessage($node, $scope, $declaredAs, $calledAs, $disallowedCalls);
					if ($message) {
						return $message;
					}
				}
			}
		} else {
			return [];
		}

		$declaredAs = $this->getFullyQualified($method->getDeclaringClass()->getDisplayName(false), $method);
		return $this->getDisallowedMessage($node, $scope, $declaredAs, $calledAs, $disallowedCalls);
	}


	private function getFullyQualified(string $class, MethodReflection $method): string
	{
		return sprintf('%s::%s', $class, $method->getName());
	}


	/**
	 * @param Name|Expr $class
	 * @param Scope $scope
	 * @return Type
	 */
	public function resolveType($class, Scope $scope): Type
	{
		return $class instanceof Name ? new ObjectType($scope->resolveName($class)) : $scope->getType($class);
	}


	/**
	 * @param Scope $scope
	 * @param DisallowedConstant $disallowedConstant
	 * @return boolean
	 */
	private function isAllowedPath(Scope $scope, DisallowedConstant $disallowedConstant): bool
	{
		foreach ($disallowedConstant->getAllowIn() as $allowedPath) {
			if (fnmatch($this->fileHelper->absolutizePath($allowedPath), $scope->getFile())) {
				return true;
			}
		}
		return false;
	}


	/**
	 * @param string $constant
	 * @param Scope $scope
	 * @param string|null $displayName
	 * @param DisallowedConstant[] $disallowedConstants
	 * @return string[]
	 */
	public function getDisallowedConstantMessage(string $constant, Scope $scope, ?string $displayName, array $disallowedConstants): array
	{
		foreach ($disallowedConstants as $disallowedConstant) {
			if ($disallowedConstant->getConstant() === $constant && !$this->isAllowedPath($scope, $disallowedConstant)) {
				return [
					sprintf(
						'Using %s%s is forbidden, %s',
						$disallowedConstant->getConstant(),
						$displayName && $displayName !== $disallowedConstant->getConstant() ? ' (as ' . $displayName . ')' : '',
						$disallowedConstant->getMessage()
					),
				];
			}
		}
		return [];
	}

}
