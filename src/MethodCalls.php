<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Broker\ClassNotFoundException;
use PHPStan\Rules\Rule;
use PHPStan\ShouldNotHappenException;

/**
 * Reports on dynamically calling a disallowed method or two.
 *
 * Static calls have a different rule, <code>StaticCalls</code>
 *
 * @package Spaze\PHPStan\Rules\Disallowed
 * @implements Rule<MethodCall>
 */
class MethodCalls implements Rule
{

	/** @var DisallowedHelper */
	private $disallowedHelper;

	/** @var DisallowedCall[] */
	private $disallowedCalls;


	/**
	 * @param DisallowedHelper $disallowedHelper
	 * @param array<array{function?:string, method?:string, message?:string, allowIn?:string[], allowParamsInAllowed?:array<integer, integer|boolean|string>, allowParamsAnywhere?:array<integer, integer|boolean|string>}> $forbiddenCalls
	 * @throws ShouldNotHappenException
	 */
	public function __construct(DisallowedHelper $disallowedHelper, array $forbiddenCalls)
	{
		$this->disallowedHelper = $disallowedHelper;
		$this->disallowedCalls = $this->disallowedHelper->createCallsFromConfig($forbiddenCalls);
	}


	public function getNodeType(): string
	{
		return MethodCall::class;
	}


	/**
	 * @param Node $node
	 * @param Scope $scope
	 * @return string[]
	 * @throws ClassNotFoundException
	 */
	public function processNode(Node $node, Scope $scope): array
	{
		/** @var MethodCall $node */
		return $this->disallowedHelper->getDisallowedMethodMessage($node->var, $node, $scope, $this->disallowedCalls);
	}

}
