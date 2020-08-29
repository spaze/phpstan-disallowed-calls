<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed;

use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Name;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;

/**
 * Reports on dynamically calling a disallowed function.
 *
 * @package spaze\PHPStan\Rules\Disallowed
 */
class FunctionCalls implements Rule
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
			if ($name === $forbiddenCall['function'] && !$this->disallowedHelper->isAllowed($scope->getFile(), $node->args, $forbiddenCall)) {
				return [
					sprintf('Calling %s is forbidden, %s', $name, $forbiddenCall['message'] ?? 'because reasons'),
				];
			}
		}

		return [];
	}
}
