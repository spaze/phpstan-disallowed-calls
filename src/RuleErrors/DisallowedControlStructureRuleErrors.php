<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\RuleErrors;

use PHPStan\Analyser\Scope;
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\ShouldNotHappenException;
use Spaze\PHPStan\Rules\Disallowed\Allowed\AllowedPath;
use Spaze\PHPStan\Rules\Disallowed\DisallowedControlStructure;
use Spaze\PHPStan\Rules\Disallowed\Formatter\Formatter;

class DisallowedControlStructureRuleErrors
{

	/** @var AllowedPath */
	private $allowedPath;

	/** @var Formatter */
	private $formatter;


	public function __construct(AllowedPath $allowedPath, Formatter $formatter)
	{
		$this->allowedPath = $allowedPath;
		$this->formatter = $formatter;
	}


	/**
	 * @param Scope $scope
	 * @param string $controlStructure
	 * @param list<DisallowedControlStructure> $disallowedControlStructures
	 * @param string $identifier
	 * @return list<RuleError>
	 * @throws ShouldNotHappenException
	 */
	public function get(Scope $scope, string $controlStructure, array $disallowedControlStructures, string $identifier): array
	{
		foreach ($disallowedControlStructures as $disallowedControlStructure) {
			if (
				$disallowedControlStructure->getControlStructure() === $controlStructure
				&& !$this->allowedPath->isAllowedPath($scope, $disallowedControlStructure)
			) {
				$errorBuilder = RuleErrorBuilder::message(sprintf(
					'Using the %s control structure is forbidden%s',
					$controlStructure,
					$this->formatter->formatDisallowedMessage($disallowedControlStructure->getMessage())
				));
				$errorBuilder->identifier($disallowedControlStructure->getErrorIdentifier() ?? $identifier);
				if ($disallowedControlStructure->getErrorTip()) {
					$errorBuilder->tip($disallowedControlStructure->getErrorTip());
				}
				return [
					$errorBuilder->build(),
				];
			}
		}
		return [];
	}

}
