<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\RuleErrors;

use PhpParser\Node\Expr\CallLike;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\FunctionReflection;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\ShouldNotHappenException;
use PHPStan\Type\Type;
use PHPStan\Type\UnionType;
use Spaze\PHPStan\Rules\Disallowed\DisallowedCall;
use Spaze\PHPStan\Rules\Disallowed\Exceptions\UnsupportedParamTypeException;
use Spaze\PHPStan\Rules\Disallowed\IsAllowedFileHelper;
use Spaze\PHPStan\Rules\Disallowed\Params\DisallowedCallParam;

class DisallowedRuleErrors
{

	/** @var IsAllowedFileHelper */
	private $isAllowedFileHelper;


	public function __construct(IsAllowedFileHelper $isAllowedFileHelper)
	{
		$this->isAllowedFileHelper = $isAllowedFileHelper;
	}


	/**
	 * @param CallLike|null $node
	 * @param Scope $scope
	 * @param string $name
	 * @param string|null $displayName
	 * @param DisallowedCall[] $disallowedCalls
	 * @param string|null $message
	 * @return RuleError[]
	 * @throws ShouldNotHappenException
	 */
	public function get(?CallLike $node, Scope $scope, string $name, ?string $displayName, array $disallowedCalls, ?string $message = null): array
	{
		foreach ($disallowedCalls as $disallowedCall) {
			$callMatches = $name === $disallowedCall->getCall() || fnmatch($disallowedCall->getCall(), $name, FNM_NOESCAPE | FNM_CASEFOLD);
			if ($callMatches && !$this->isAllowed($scope, $node, $disallowedCall)) {
				$errorBuilder = RuleErrorBuilder::message(sprintf(
					$message ?? 'Calling %s is forbidden, %s%s',
					($displayName && $displayName !== $name) ? "{$name}() (as {$displayName}())" : "{$name}()",
					$disallowedCall->getMessage(),
					$disallowedCall->getCall() !== $name ? " [{$name}() matches {$disallowedCall->getCall()}()]" : ''
				));
				if ($disallowedCall->getErrorIdentifier()) {
					$errorBuilder->identifier($disallowedCall->getErrorIdentifier());
				}
				if ($disallowedCall->getErrorTip()) {
					$errorBuilder->tip($disallowedCall->getErrorTip());
				}
				return [
					$errorBuilder->build(),
				];
			}
		}
		return [];
	}


	private function isAllowed(Scope $scope, ?CallLike $node, DisallowedCall $disallowedCall): bool
	{
		foreach ($disallowedCall->getAllowInCalls() as $call) {
			if ($this->callMatches($scope, $call)) {
				return $this->hasAllowedParamsInAllowed($scope, $node, $disallowedCall);
			}
		}
		foreach ($disallowedCall->getAllowExceptInCalls() as $call) {
			if (!$this->callMatches($scope, $call)) {
				return true;
			}
		}
		foreach ($disallowedCall->getAllowIn() as $allowedPath) {
			if ($this->isAllowedFileHelper->matches($scope, $allowedPath)) {
				return $this->hasAllowedParamsInAllowed($scope, $node, $disallowedCall);
			}
		}
		if ($disallowedCall->getAllowExceptIn()) {
			foreach ($disallowedCall->getAllowExceptIn() as $allowedExceptPath) {
				if ($this->isAllowedFileHelper->matches($scope, $allowedExceptPath)) {
					return false;
				}
			}
			return true;
		}
		if ($disallowedCall->getAllowExceptParams()) {
			return $this->hasAllowedParams($scope, $node, $disallowedCall->getAllowExceptParams(), false);
		}
		if ($disallowedCall->getAllowParamsAnywhere()) {
			return $this->hasAllowedParams($scope, $node, $disallowedCall->getAllowParamsAnywhere(), true);
		}
		return false;
	}


	/**
	 * @param Scope $scope
	 * @param CallLike|null $node
	 * @param array<int|string, DisallowedCallParam> $allowConfig
	 * @param bool $paramsRequired
	 * @return bool
	 */
	private function hasAllowedParams(Scope $scope, ?CallLike $node, array $allowConfig, bool $paramsRequired): bool
	{
		if (!$node) {
			return true;
		}

		foreach ($allowConfig as $param) {
			$type = $this->getArgType($node, $scope, $param);
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
					if (!$param->matches($type)) {
						return false;
					}
				} catch (UnsupportedParamTypeException $e) {
					return !$paramsRequired;
				}
			}
		}
		return true;
	}


	/**
	 * @param CallLike $node
	 * @param Scope $scope
	 * @param DisallowedCallParam $param
	 * @return Type|null
	 */
	private function getArgType(CallLike $node, Scope $scope, DisallowedCallParam $param): ?Type
	{
		foreach ($node->getArgs() as $arg) {
			if ($arg->name && $arg->name->name === $param->getName()) {
				$found = $arg;
				break;
			}
		}
		if (!isset($found)) {
			$found = $node->getArgs()[$param->getPosition() - 1] ?? null;
		}
		return isset($found) ? $scope->getType($found->value) : null;
	}


	private function callMatches(Scope $scope, string $call): bool
	{
		if ($scope->getFunction() instanceof MethodReflection) {
			$name = $this->getFullyQualified($scope->getFunction()->getDeclaringClass()->getDisplayName(false), $scope->getFunction());
		} elseif ($scope->getFunction() instanceof FunctionReflection) {
			$name = $scope->getFunction()->getName();
		} else {
			$name = '';
		}
		return fnmatch($call, $name, FNM_NOESCAPE | FNM_CASEFOLD);
	}


	private function hasAllowedParamsInAllowed(Scope $scope, ?CallLike $node, DisallowedCall $disallowedCall): bool
	{
		if ($disallowedCall->getAllowExceptParamsInAllowed()) {
			return $this->hasAllowedParams($scope, $node, $disallowedCall->getAllowExceptParamsInAllowed(), false);
		}
		if ($disallowedCall->getAllowParamsInAllowed()) {
			return $this->hasAllowedParams($scope, $node, $disallowedCall->getAllowParamsInAllowed(), true);
		}
		return true;
	}


	public function getFullyQualified(string $class, MethodReflection $method): string
	{
		return sprintf('%s::%s', $class, $method->getName());
	}

}
