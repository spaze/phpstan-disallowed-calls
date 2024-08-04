<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Usages;

use PhpParser\Node;
use PhpParser\Node\Expr\Variable;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleError;
use PHPStan\ShouldNotHappenException;
use Spaze\PHPStan\Rules\Disallowed\DisallowedVariable;
use Spaze\PHPStan\Rules\Disallowed\RuleErrors\DisallowedVariableRuleErrors;

/**
 * Reports on a variable name usage.
 *
 * @package Spaze\PHPStan\Rules\Disallowed
 * @implements Rule<Variable>
 */
class VariableUsages implements Rule
{

	/**
	 * @param list<DisallowedVariable> $disallowedVariables
	 */
	public function __construct(
		private readonly DisallowedVariableRuleErrors $disallowedVariableRuleErrors,
		private readonly array $disallowedVariables,
	) {
	}


	public function getNodeType(): string
	{
		return Variable::class;
	}


	/**
	 * @return list<RuleError>
	 * @throws ShouldNotHappenException
	 */
	public function processNode(Node $node, Scope $scope): array
	{
		$variableName = $node->name;
		if (!is_string($variableName)) {
			return [];
		}

		return $this->disallowedVariableRuleErrors->get('$' . $variableName, $scope, $this->disallowedVariables);
	}

}
