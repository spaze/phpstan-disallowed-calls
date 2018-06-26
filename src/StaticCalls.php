<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed;

use PhpParser\Node;
use PhpParser\Node\Expr\StaticCall;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleLevelHelper;

/**
 * Reports on statically calling a forbidden method or two.
 *
 * Dynamic calls have a different rule, <code>MethodCalls</code>
 *
 * Specify required arguments in a config file, example:
 * <code>
 * arguments:
 *   forbiddenCalls:
 *     -
 *       method: 'Tracy\Debugger::log()'
 *       message: 'use our own logger instead'
 *     -
 *       method: 'Foo\Bar::baz()'
 *       message: 'waldo instead'
 * </code>
 *
 * @package spaze\PHPStan\Rules\Disallowed
 */
class StaticCalls implements Rule
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
		return StaticCall::class;
	}


	/**
	 * @param Node $node
	 * @param Scope $scope
	 * @return string[]
	 */
	public function processNode(Node $node, Scope $scope): array
	{
		/** @var StaticCall $node */
		if (!is_string($node->name)) {
			return [];
		}

		$fullyQualified = "{$node->class}::{$node->name}()";
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
