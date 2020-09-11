<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PHPStan\Analyser\Scope;
use PHPStan\Broker\ClassNotFoundException;
use PHPStan\File\FileHelper;
use PHPStan\Reflection\MethodReflection;
use PHPStan\ShouldNotHappenException;
use PHPStan\Type\ConstantScalarType;
use PHPStan\Type\ObjectType;
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
	 * @param Arg[] $args
	 * @param DisallowedCall $disallowedCall
	 * @return boolean
	 */
	public function isAllowed(Scope $scope, array $args, DisallowedCall $disallowedCall): bool
	{
		foreach ($disallowedCall->getAllowIn() as $allowedPath) {
			if (fnmatch($this->fileHelper->absolutizePath($allowedPath), $scope->getFile())
				&& $this->hasAllowedParams($scope, $args, $disallowedCall->getAllowParamsInAllowed(), true)
			) {
				return true;
			}
		}
		return $this->hasAllowedParams($scope, $args, $disallowedCall->getAllowParamsAnywhere(), false);
	}


	/**
	 * @param Scope $scope
	 * @param Arg[] $args
	 * @param array<integer, integer|boolean|string> $allowConfig
	 * @param boolean $default
	 * @return boolean
	 */
	private function hasAllowedParams(Scope $scope, array $args, array $allowConfig, bool $default): bool
	{
		$disallowed = false;
		foreach ($allowConfig as $param => $value) {
			$arg = $args[$param - 1] ?? null;
			$type = $arg ? $scope->getType($arg->value) : null;
			if ($arg && $type instanceof ConstantScalarType) {
				$disallowed = $disallowed || ($value !== $type->getValue());
			} else {
				$disallowed = true;
			}
		}
		if (count($allowConfig) > 0) {
			return !$disallowed;
		}
		return $default;
	}


	/**
	 * @param array<array{function?:string, method?:string, message?:string, allowIn?:string[], allowParamsInAllowed?:array<integer, integer|boolean|string>, allowParamsAnywhere?:array<integer, integer|boolean|string>}> $config
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
			$calls[] = new DisallowedCall(
				$call,
				$disallowedCall['message'] ?? null,
				$disallowedCall['allowIn'] ?? [],
				$disallowedCall['allowParamsInAllowed'] ?? [],
				$disallowedCall['allowParamsAnywhere'] ?? []
			);
		}
		return $calls;
	}


	/**
	 * @param FuncCall|MethodCall|StaticCall $node
	 * @param Scope $scope
	 * @param string $name
	 * @param string|null $displayName
	 * @param DisallowedCall[] $disallowedCalls
	 * @return string[]
	 */
	public function getDisallowedMessage(Node $node, Scope $scope, string $name, ?string $displayName, array $disallowedCalls): array
	{
		foreach ($disallowedCalls as $disallowedCall) {
			if ($name === $disallowedCall->getCall() && !$this->isAllowed($scope, $node->args, $disallowedCall)) {
				$call = ($displayName && $displayName !== $name ? "{$name} (as {$displayName})" : $name);
				return [
					sprintf('Calling %s is forbidden, %s', $call, $disallowedCall->getMessage()),
				];
			}
		}
		return [];
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

		if ($class instanceof Name) {
			$calledOnType = new ObjectType($scope->resolveName($class));
		} else {
			$calledOnType = $scope->getType($class);
		}

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

		$declaredAs = $this->getFullyQualified($method->getDeclaringClass()->getDisplayName(), $method);
		return $this->getDisallowedMessage($node, $scope, $declaredAs, $calledAs, $disallowedCalls);
	}


	private function getFullyQualified(string $class, MethodReflection $method): string
	{
		return sprintf('%s::%s()', $class, $method->getName());
	}

}
