<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\RuleErrors;

use PHPStan\Analyser\Scope;
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\ShouldNotHappenException;
use Spaze\PHPStan\Rules\Disallowed\Allowed\AllowedPath;
use Spaze\PHPStan\Rules\Disallowed\DisallowedConstant;

class DisallowedConstantRuleErrors
{


	/** @var AllowedPath */
	private $allowedPath;


	public function __construct(AllowedPath $allowedPath)
	{
		$this->allowedPath = $allowedPath;
	}


	/**
	 * @param string $constant
	 * @param Scope $scope
	 * @param string|null $displayName
	 * @param list<DisallowedConstant> $disallowedConstants
	 * @return list<RuleError>
	 * @throws ShouldNotHappenException
	 */
	public function get(string $constant, Scope $scope, ?string $displayName, array $disallowedConstants): array
	{
		foreach ($disallowedConstants as $disallowedConstant) {
			if ($disallowedConstant->getConstant() === $constant && !$this->allowedPath->isAllowedPath($scope, $disallowedConstant)) {
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
