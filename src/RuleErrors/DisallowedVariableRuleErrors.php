<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\RuleErrors;

use PHPStan\Analyser\Scope;
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\ShouldNotHappenException;
use Spaze\PHPStan\Rules\Disallowed\Allowed\AllowedPath;
use Spaze\PHPStan\Rules\Disallowed\DisallowedVariable;
use Spaze\PHPStan\Rules\Disallowed\Formatter\Formatter;

class DisallowedVariableRuleErrors
{

	public function __construct(
		private readonly AllowedPath $allowedPath,
		private readonly Formatter $formatter,
	) {
	}


	/**
	 * @param list<DisallowedVariable> $disallowedVariables
	 * @return list<RuleError>
	 * @throws ShouldNotHappenException
	 */
	public function get(string $variable, Scope $scope, array $disallowedVariables): array
	{
		foreach ($disallowedVariables as $disallowedVariable) {
			if ($disallowedVariable->getVariable() === $variable && !$this->allowedPath->isAllowedPath($scope, $disallowedVariable)) {
				$errorBuilder = RuleErrorBuilder::message(sprintf(
					'Using %s is forbidden%s',
					$disallowedVariable->getVariable(),
					$this->formatter->formatDisallowedMessage($disallowedVariable->getMessage()),
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
