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


	private function isAllowedPath(Scope $scope, DisallowedVariable $disallowedVariable): bool
	{
		foreach ($disallowedVariable->getAllowIn() as $allowedPath) {
			if ($this->isAllowedFileHelper->matches($scope, $allowedPath)) {
				return true;
			}
		}
		return false;
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
			if ($disallowedVariable->getVariable() === $variable && !$this->isAllowedPath($scope, $disallowedVariable)) {
				return [
					RuleErrorBuilder::message(sprintf(
						'Using %s is forbidden, %s',
						$disallowedVariable->getVariable(),
						$disallowedVariable->getMessage()
					))
						->identifier($disallowedVariable->getErrorIdentifier())
						->build(),
				];
			}
		}

		return [];
	}

}
