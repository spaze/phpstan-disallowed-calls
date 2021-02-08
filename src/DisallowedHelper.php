<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed;

use PhpParser\Node;
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
	 * @param FuncCall|MethodCall|StaticCall|null $node
	 * @param DisallowedCall $disallowedCall
	 * @return boolean
	 */
	private function isAllowed(Scope $scope, ?Node $node, DisallowedCall $disallowedCall): bool
	{
		foreach ($disallowedCall->getAllowIn() as $allowedPath) {
			$match = fnmatch($this->fileHelper->absolutizePath($allowedPath), $scope->getFile());
			if ($match && $this->hasAllowedParams($scope, $node, $disallowedCall->getAllowParamsInAllowed(), true)) {
				return true;
			}
		}
		return $this->hasAllowedParams($scope, $node, $disallowedCall->getAllowParamsAnywhere(), false);
	}


	/**
	 * @param Scope $scope
	 * @param FuncCall|MethodCall|StaticCall|null $node
	 * @param array<integer, integer|boolean|string> $allowConfig
	 * @param boolean $default
	 * @return boolean
	 */
	private function hasAllowedParams(Scope $scope, ?Node $node, array $allowConfig, bool $default): bool
	{
		if (!$node) {
			return $default;
		}

		$disallowed = false;
		foreach ($allowConfig as $param => $value) {
			$arg = $node->args[$param - 1] ?? null;
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
			$disallowedCall = new DisallowedCall(
				$call,
				$disallowedCall['message'] ?? null,
				$disallowedCall['allowIn'] ?? [],
				$disallowedCall['allowParamsInAllowed'] ?? [],
				$disallowedCall['allowParamsAnywhere'] ?? []
			);
			$calls[$disallowedCall->getCall()] = $disallowedCall;
		}
		return array_values($calls);
	}


	/**
	 * @param array<array{constant?:string, message?:string, allowIn?:string[]}> $config
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
			$disallowedConstant = new DisallowedConstant(
				$constant,
				$disallowedConstant['message'] ?? null,
				$disallowedConstant['allowIn'] ?? []
			);
			$constants[$disallowedConstant->getConstant()] = $disallowedConstant;
		}
		return array_values($constants);
	}


	/**
	 * @param FuncCall|MethodCall|StaticCall|null $node
	 * @param Scope $scope
	 * @param string $name
	 * @param string|null $displayName
	 * @param DisallowedCall[] $disallowedCalls
	 * @return string[]
	 */
	public function getDisallowedMessage(?Node $node, Scope $scope, string $name, ?string $displayName, array $disallowedCalls): array
	{
		foreach ($disallowedCalls as $disallowedCall) {
			if ($this->callMatches($disallowedCall, $name) && !$this->isAllowed($scope, $node, $disallowedCall)) {
				return [
					sprintf(
						'Calling %s is forbidden, %s%s',
						($displayName && $displayName !== $name) ? "{$name}() (as {$displayName}())" : "{$name}()",
						$disallowedCall->getMessage(),
						$disallowedCall->getCall() !== $name ? " [{$name}() matches {$disallowedCall->getCall()}()]" : ''
					),
				];
			}
		}
		return [];
	}


	private function callMatches(DisallowedCall $disallowedCall, string $name): bool
	{
		if ($disallowedCall->getCall()[-1] === '*') {
			return strpos($name, trim($disallowedCall->getCall(), '*')) === 0;
		} else {
			return $name === $disallowedCall->getCall();
		}
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

		$declaredAs = $this->getFullyQualified($method->getDeclaringClass()->getDisplayName(), $method);
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
