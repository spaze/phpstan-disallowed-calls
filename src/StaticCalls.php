<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PHPStan\Analyser\Scope;
use PHPStan\Broker\ClassNotFoundException;
use PHPStan\Rules\Rule;
use PHPStan\ShouldNotHappenException;
use PHPStan\Type\ObjectType;

/**
 * Reports on statically calling a disallowed method or two.
 *
 * Dynamic calls have a different rule, <code>MethodCalls</code>
 *
 * @package Spaze\PHPStan\Rules\Disallowed
 * @implements Rule<StaticCall>
 */
class StaticCalls implements Rule
{

	/** @var DisallowedHelper */
	private $disallowedHelper;

	/** @var array{function?:string, method?:string, message?:string, allowIn?:string[], allowParamsInAllowed?:array<integer, integer|boolean|string>}[] */
	private $forbiddenCalls;


	/**
	 * @param DisallowedHelper $disallowedHelper
	 * @param array{function?:string, method?:string, message?:string, allowIn?:string[], allowParamsInAllowed?:array<integer, integer|boolean|string>}[] $forbiddenCalls
	 */
	public function __construct(DisallowedHelper $disallowedHelper, array $forbiddenCalls)
	{
		$this->disallowedHelper = $disallowedHelper;
		$this->forbiddenCalls = $forbiddenCalls;
	}


	public function getNodeType(): string
	{
		return StaticCall::class;
	}


	/**
	 * @param Node $node
	 * @param Scope $scope
	 * @return string[]
	 * @throws ShouldNotHappenException
	 * @throws ClassNotFoundException
	 */
	public function processNode(Node $node, Scope $scope): array
	{
		/** @var StaticCall $node */
		if (!($node->name instanceof Identifier)) {
			return [];
		}

		$fullyQualified = $this->getMethod($node->class, $node->name->name, $scope);
		foreach ($this->forbiddenCalls as $forbiddenCall) {
			if (!isset($forbiddenCall['method'])) {
				throw new ShouldNotHappenException("Key 'method' missing in disallowedStaticCalls configuration");
			}
			if ($fullyQualified === $forbiddenCall['method'] && !$this->disallowedHelper->isAllowed($scope, $node->args, $forbiddenCall)) {
				return [
					sprintf('Calling %s is forbidden, %s', $fullyQualified, $forbiddenCall['message'] ?? 'because reasons'),
				];
			}
		}

		return [];
	}


	/**
	 * @param Name|Expr $class
	 * @param string $methodName
	 * @param Scope $scope
	 * @return string
	 * @throws ClassNotFoundException
	 */
	private function getMethod($class, string $methodName, Scope $scope): string
	{
		if ($class instanceof Name) {
			$calledOnType = new ObjectType($scope->resolveName($class));
		} else {
			$calledOnType = $scope->getType($class);
		}

		$method = $calledOnType->getMethod($methodName, $scope);
		return sprintf('%s::%s()', $method->getDeclaringClass()->getDisplayName(), $method->getName());
	}

}
