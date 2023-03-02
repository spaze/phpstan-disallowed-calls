<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\RuleErrors;

use PHPStan\Analyser\Scope;
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\ShouldNotHappenException;
use Spaze\PHPStan\Rules\Disallowed\DisallowedConstant;
use Spaze\PHPStan\Rules\Disallowed\IsAllowedFileHelper;

class DisallowedConstantRuleErrors
{


	/** @var IsAllowedFileHelper */
	private $isAllowedFileHelper;


	public function __construct(IsAllowedFileHelper $isAllowedFileHelper)
	{
		$this->isAllowedFileHelper = $isAllowedFileHelper;
	}


	/**
	 * @param string $constant
	 * @param Scope $scope
	 * @param string|null $displayName
	 * @param DisallowedConstant[] $disallowedConstants
	 * @return RuleError[]
	 * @throws ShouldNotHappenException
	 */
	public function get(string $constant, Scope $scope, ?string $displayName, array $disallowedConstants): array
	{
		foreach ($disallowedConstants as $disallowedConstant) {
			if ($disallowedConstant->getConstant() === $constant && !$this->isAllowedFileHelper->isAllowedPath($scope, $disallowedConstant)) {
				$errorBuilder = RuleErrorBuilder::message(sprintf(
					'Using %s%s is forbidden, %s',
					$disallowedConstant->getConstant(),
					$displayName && $displayName !== $disallowedConstant->getConstant() ? ' (as ' . $displayName . ')' : '',
					$disallowedConstant->getMessage()
				));
				if ($disallowedConstant->getErrorIdentifier()) {
					$errorBuilder->identifier($disallowedConstant->getErrorIdentifier());
				}
				if ($disallowedConstant->getErrorTip()) {
					$errorBuilder->tip($disallowedConstant->getErrorTip());
				}
				return [
					$errorBuilder->build(),
				];
			}
		}
		return [];
	}

}
