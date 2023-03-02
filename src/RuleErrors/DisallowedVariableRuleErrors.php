<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\RuleErrors;

use PHPStan\Analyser\Scope;
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\ShouldNotHappenException;
use Spaze\PHPStan\Rules\Disallowed\AllowedPath;
use Spaze\PHPStan\Rules\Disallowed\DisallowedVariable;

class DisallowedVariableRuleErrors
{

	/** @var AllowedPath */
	private $isAllowedFileHelper;


	public function __construct(AllowedPath $isAllowedFileHelper)
	{
		$this->isAllowedFileHelper = $isAllowedFileHelper;
	}


	/**
	 * @param string $variable
	 * @param Scope $scope
	 * @param DisallowedVariable[] $disallowedVariables
	 * @return RuleError[]
	 * @throws ShouldNotHappenException
	 */
	public function get(string $variable, Scope $scope, array $disallowedVariables): array
	{
		foreach ($disallowedVariables as $disallowedVariable) {
			if ($disallowedVariable->getVariable() === $variable && !$this->isAllowedFileHelper->isAllowedPath($scope, $disallowedVariable)) {
				$errorBuilder = RuleErrorBuilder::message(sprintf(
					'Using %s is forbidden, %s',
					$disallowedVariable->getVariable(),
					$disallowedVariable->getMessage()
				));
				if ($disallowedVariable->getErrorIdentifier()) {
					$errorBuilder->identifier($disallowedVariable->getErrorIdentifier());
				}
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
