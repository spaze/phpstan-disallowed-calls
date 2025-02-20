<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\RuleErrors;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\ShouldNotHappenException;
use Spaze\PHPStan\Rules\Disallowed\Allowed\Allowed;
use Spaze\PHPStan\Rules\Disallowed\DisallowedVariable;
use Spaze\PHPStan\Rules\Disallowed\Formatter\Formatter;

class DisallowedVariableRuleErrors
{

	private Allowed $allowed;

	private Formatter $formatter;


	public function __construct(Allowed $allowed, Formatter $formatter)
	{
		$this->allowed = $allowed;
		$this->formatter = $formatter;
	}


	/**
	 * @param string $variable
	 * @param Node $node
	 * @param Scope $scope
	 * @param list<DisallowedVariable> $disallowedVariables
	 * @return list<RuleError>
	 * @throws ShouldNotHappenException
	 */
	public function get(string $variable, Node $node, Scope $scope, array $disallowedVariables): array
	{
		foreach ($disallowedVariables as $disallowedVariable) {
			if ($disallowedVariable->getVariable() === $variable && !$this->allowed->isAllowed($node, $scope, null, $disallowedVariable)) {
				$errorBuilder = RuleErrorBuilder::message(sprintf(
					'Using %s is forbidden%s',
					$disallowedVariable->getVariable(),
					$this->formatter->formatDisallowedMessage($disallowedVariable->getMessage())
				));
				$errorBuilder->identifier($disallowedVariable->getErrorIdentifier() ?? ErrorIdentifiers::DISALLOWED_VARIABLE);
				if ($disallowedVariable->getErrorTip()) {
					$errorBuilder->tip($disallowedVariable->getErrorTip());
				}
				return [
					$errorBuilder->build(),
				];
			}
		}

		return [];
	}

}
