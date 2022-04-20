<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\Usages;

use PhpParser\Node;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\Variable;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleError;
use PHPStan\ShouldNotHappenException;
use Spaze\PHPStan\Rules\Disallowed\DisallowedVariable;
use Spaze\PHPStan\Rules\Disallowed\DisallowedVariableHelper;

/**
 * Reports on a variable name usage.
 *
 * @package Spaze\PHPStan\Rules\Disallowed
 * @implements Rule<Variable>
 */
class VariableUsages implements Rule
{

	/** @var DisallowedVariableHelper */
	private $disallowedHelper;

	/** @var DisallowedVariable[] */
	private $disallowedVariables;


	/**
	 * @param DisallowedVariableHelper $disallowedVariableHelper
	 * @param DisallowedVariable[] $disallowedVariables
	 */
	public function __construct(DisallowedVariableHelper $disallowedVariableHelper, array $disallowedVariables)
	{
		$this->disallowedHelper = $disallowedVariableHelper;
		$this->disallowedVariables = $disallowedVariables;
	}


	public function getNodeType(): string
	{
		return Variable::class;
	}


	private function isVariableBeingAssigned(Variable $variable): bool
	{
		$parentNode = $variable->getAttribute('parent');
		if (!($parentNode instanceof Assign)) {
			return false;
		}

		return $variable === $parentNode->var;
	}


	/**
	 * @param Variable $node
	 * @param Scope $scope
	 * @return RuleError[]
	 * @throws ShouldNotHappenException
	 */
	public function processNode(Node $node, Scope $scope): array
	{
		if (!($node instanceof Variable)) {
			throw new ShouldNotHappenException(sprintf('$node should be %s but is %s', Variable::class, get_class($node)));
		}

		// If it's an assignment, allow it since it might define the variable in the current scope.
		if ($this->isVariableBeingAssigned($node)) {
			return [];
		}

		$variableName = $node->name;
		if (!is_string($variableName)) {
			return [];
		}

		$definedVariables = $scope->getDefinedVariables();
		if (in_array($variableName, $definedVariables, true)) {
			return [];
		}

		return $this->disallowedHelper->getDisallowedMessage('$' . $variableName, $scope, $this->disallowedVariables);
	}

}
