<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed;

use PHPStan\Analyser\Scope;
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;

class DisallowedVariableHelper
{

	/** @var IsAllowedFileHelper */
	private $isAllowedFileHelper;


	public function __construct(IsAllowedFileHelper $isAllowedFileHelper)
	{
		$this->isAllowedFileHelper = $isAllowedFileHelper;
	}


	/**
	 * @param string $variable
	 * @param Scope $scope
	 * @param DisallowedVariable[] $disallowedVariables
	 * @return RuleError[]
	 */
	public function getDisallowedMessage(string $variable, Scope $scope, array $disallowedVariables): array
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
