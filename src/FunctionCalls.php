<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed;

use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Name;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;

/**
 * Reports on dynamically calling a forbidden function.
 *
 * Specify required arguments in a config file, example:
 * <code>
 * arguments:
 *   forbiddenCalls:
 *     -
 *       function: 'var_dump()'
 *       message: 'use logger instead'
 *     -
 *       function: 'Foo\Bar\baz()'
 *       message: 'waldo instead'
 * </code>
 *
 * @package spaze\PHPStan\Rules\Disallowed
 */
class FunctionCalls implements Rule
{

	/** @var string[][] */
	private $forbiddenCalls;


	public function __construct(array $forbiddenCalls)
	{
		$this->forbiddenCalls = $forbiddenCalls;
	}


	public function getNodeType(): string
	{
		return FuncCall::class;
	}


	/**
	 * @param FuncCall $node
	 * @param Scope $scope
	 * @return string[]
	 */
	public function processNode(Node $node, Scope $scope): array
	{
		if (!($node->name instanceof Name)) {
			return [];
		}

		$name = $node->name . '()';
		foreach ($this->forbiddenCalls as $forbiddenCall) {
			if ($name === $forbiddenCall['function']) {
				return [
					sprintf('Calling %s is forbidden, %s', $name, $forbiddenCall['message'] ?? 'because reasons'),
				];
			}
		}

		return [];
	}
}
