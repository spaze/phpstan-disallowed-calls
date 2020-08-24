<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed;

use PhpParser\Node;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Identifier;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;

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
 *       allowIn:
 *         - optional/path/to/*.tests.php
 *         - another/file.php
 *     -
 *       method: 'Foo\Bar::baz()'
 *       message: 'waldo instead'
 * </code>
 *
 * @package spaze\PHPStan\Rules\Disallowed
 */
class StaticCalls implements Rule
{

	/** @var DisallowedHelper */
	private $disallowedHelper;

	/** @var string[][] */
	private $forbiddenCalls;


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
	 */
	public function processNode(Node $node, Scope $scope): array
	{
		/** @var StaticCall $node */
		if (!($node->name instanceof Identifier)) {
			return [];
		}

		$name = $node->name->name;
		$fullyQualified = "{$node->class}::{$name}()";
		foreach ($this->forbiddenCalls as $forbiddenCall) {
			if ($fullyQualified === $forbiddenCall['method'] && !$this->disallowedHelper->isAllowed($scope->getFile(), $forbiddenCall)) {
				return [
					sprintf('Calling %s is forbidden, %s', $fullyQualified, $forbiddenCall['message'] ?? 'because reasons'),
				];
			}
		}

		return [];
	}

}
