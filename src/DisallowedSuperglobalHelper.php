<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed;

use PHPStan\Analyser\Scope;
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;

class DisallowedSuperglobalHelper
{

	/** @var IsAllowedFileHelper */
	private $isAllowedFileHelper;


	public function __construct(IsAllowedFileHelper $isAllowedFileHelper)
	{
		$this->isAllowedFileHelper = $isAllowedFileHelper;
	}


	private function isAllowedPath(Scope $scope, DisallowedSuperglobal $disallowedSuperglobal): bool
	{
		foreach ($disallowedSuperglobal->getAllowIn() as $allowedPath) {
			if ($this->isAllowedFileHelper->matches($scope, $allowedPath)) {
				return true;
			}
		}
		return false;
	}


	/**
	 * @param string $superglobal
	 * @param Scope $scope
	 * @param DisallowedSuperglobal[] $disallowedSuperglobals
	 * @return RuleError[]
	 */
	public function getDisallowedMessage(string $superglobal, Scope $scope, array $disallowedSuperglobals): array
	{
		foreach ($disallowedSuperglobals as $disallowedSuperglobal) {
			if ($disallowedSuperglobal->getSuperglobal() === $superglobal && !$this->isAllowedPath($scope, $disallowedSuperglobal)) {
				return [
					RuleErrorBuilder::message(sprintf(
						'Using %s is forbidden, %s',
						$disallowedSuperglobal->getSuperglobal(),
						$disallowedSuperglobal->getMessage()
					))
						->identifier($disallowedSuperglobal->getErrorIdentifier())
						->build(),
				];
			}
		}

		return [];
	}

}
