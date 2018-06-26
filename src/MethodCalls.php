<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleLevelHelper;
use PHPStan\ShouldNotHappenException;

/**
 * Reports on dynamically calling a forbidden method or two.
 *
 * Static calls have a different rule, <code>StaticCalls</code>
 *
 * Specify required arguments in a config file, example:
 * <code>
 * arguments:
 *   forbiddenCalls:
 *     -
 *       method: 'Tracy\ILogger::log()'
 *       message: 'use our own logger instead'
 *     -
 *       method: 'Foo\Bar::baz()'
 *       message: 'waldo instead'
 * </code>
 *
 * @package spaze\PHPStan\Rules\Disallowed
 */
class MethodCalls implements Rule
{

	/** @var RuleLevelHelper */
	private $ruleLevelHelper;

	/** @var string[][] */
	private $forbiddenCalls;


	public function __construct(RuleLevelHelper $ruleLevelHelper, array $forbiddenCalls)
	{
		$this->ruleLevelHelper = $ruleLevelHelper;
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
		if (!is_string($node->name)) {
			return [];
		}

		$typeResult = $this->ruleLevelHelper->findTypeToCheck(
			$scope,
			$node->var,
			sprintf('Call to method %s() on an unknown class %%s.', $node->name)
		);
		if (count($typeResult->getReferencedClasses()) > 1) {
			throw new ShouldNotHappenException('One too many referenced classes: ' . implode(', ', $typeResult->getReferencedClasses()));
		}

		$fullyQualified = current($typeResult->getReferencedClasses()) . "::{$node->name}()";
		foreach ($this->forbiddenCalls as $forbiddenCall) {
			if ($fullyQualified === $forbiddenCall['method']) {
				return [
					sprintf('Calling %s is forbidden, %s', $fullyQualified, $forbiddenCall['message'] ?? 'because reasons'),
				];
			}
		}

		return [];
	}

}
