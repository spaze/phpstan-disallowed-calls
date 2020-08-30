<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use PHPStan\Analyser\Scope;
use PHPStan\Broker\Broker;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleLevelHelper;
use PHPStan\ShouldNotHappenException;
use PHPStan\Type\Type;

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

	/** @var RuleLevelHelper */
	private $ruleLevelHelper;

	/** @var DisallowedHelper */
	private $disallowedHelper;

	/** @var array{function?:string, method?:string, message?:string, allowIn?:string[], allowParamsInAllowed?:array<integer, integer|boolean|string>}[] */
	private $forbiddenCalls;


	/**
	 * @param Broker $broker
	 * @param DisallowedHelper $disallowedHelper
	 * @param array{function?:string, method?:string, message?:string, allowIn?:string[], allowParamsInAllowed?:array<integer, integer|boolean|string>}[] $forbiddenCalls
	 */
	public function __construct(Broker $broker, DisallowedHelper $disallowedHelper, array $forbiddenCalls)
	{
		$this->ruleLevelHelper = new RuleLevelHelper($broker, true, false, true);
		$this->disallowedHelper = $disallowedHelper;
		$this->forbiddenCalls = $forbiddenCalls;
	}


	public function getNodeType(): string
	{
		return MethodCall::class;
	}


	/**
	 * @param Node $node
	 * @param Scope $scope
	 * @return string[]
	 * @throws ShouldNotHappenException
	 */
	public function processNode(Node $node, Scope $scope): array
	{
		/** @var MethodCall $node */
		if (!($node->name instanceof Identifier)) {
			return [];
		}

		$name = $node->name->name;
		$typeResult = $this->ruleLevelHelper->findTypeToCheck(
			$scope,
			$node->var,
			sprintf('Call to method %s() on an unknown class %%s.', $name),
			static function (Type $type) use ($name): bool {
				return $type->canCallMethods()->yes() && $type->hasMethod($name)->yes();
			}
		);

		foreach ($typeResult->getReferencedClasses() as $referencedClass) {
			$fullyQualified = "{$referencedClass}::{$name}()";
			foreach ($this->forbiddenCalls as $forbiddenCall) {
				if (!isset($forbiddenCall['method'])) {
					throw new ShouldNotHappenException("Key 'method' missing in disallowedMethodCalls configuration");
				}
				if ($fullyQualified === $forbiddenCall['method'] && !$this->disallowedHelper->isAllowed($scope, $node->args, $forbiddenCall)) {
					return [
						sprintf('Calling %s is forbidden, %s', $fullyQualified, $forbiddenCall['message'] ?? 'because reasons'),
					];
				}
			}
		}

		return [];
	}

}
