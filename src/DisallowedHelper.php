<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use PHPStan\Analyser\Scope;
use PHPStan\File\FileHelper;
use PHPStan\ShouldNotHappenException;
use PHPStan\Type\ConstantScalarType;

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
			$disallowed = $arg && $type instanceof ConstantScalarType
				? $disallowed || ($value !== $type->getValue())
				: true;
		}
		if ($allowConfig !== []) {
			return !$disallowed;
		}
		return $default;
	}


	/**
	 * @param array<array{function?:string, method?:string, message?:string, allowIn?:string[], allowParamsInAllowed?:array<integer, integer|boolean|string>, allowParamsAnywhere?:array<integer, integer|boolean|string>}> $config
	 * @return DisallowedCall[]
	 */
	public function createCallsFromConfig(array $config): array
	{
		return array_map(
			function ($disallowedCall) {
				$call = $disallowedCall['function'] ?? $disallowedCall['method'] ?? null;
				if (!$call) {
					throw new ShouldNotHappenException("Either 'method' or 'function' must be set in configuration items");
				}
				return new DisallowedCall(
					$call,
					$disallowedCall['message'] ?? null,
					$disallowedCall['allowIn'] ?? [],
					$disallowedCall['allowParamsInAllowed'] ?? [],
					$disallowedCall['allowParamsAnywhere'] ?? []
				);
			},
			$config
		);
	}


	/**
	 * @param FuncCall|MethodCall|StaticCall $node
	 * @param Scope $scope
	 * @param string $name
	 * @param DisallowedCall[] $disallowedCalls
	 * @return string[]
	 */
	public function getDisallowedMessage(Node $node, Scope $scope, string $name, array $disallowedCalls): array
	{
		foreach ($disallowedCalls as $disallowedCall) {
			if ($name === $disallowedCall->getCall() && !$this->isAllowed($scope, $node->args, $disallowedCall)) {
				return [
					sprintf('Calling %s is forbidden, %s', $name, $disallowedCall->getMessage()),
				];
			}
		}
		return [];
	}

}
