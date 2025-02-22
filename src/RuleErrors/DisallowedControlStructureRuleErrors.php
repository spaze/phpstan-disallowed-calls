<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\RuleErrors;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\ShouldNotHappenException;
use Spaze\PHPStan\Rules\Disallowed\Allowed\Allowed;
use Spaze\PHPStan\Rules\Disallowed\DisallowedControlStructure;
use Spaze\PHPStan\Rules\Disallowed\Formatter\Formatter;

class DisallowedControlStructureRuleErrors
{

	private Allowed $allowed;

	private Formatter $formatter;


	public function __construct(Allowed $allowed, Formatter $formatter)
	{
		$this->allowed = $allowed;
		$this->formatter = $formatter;
	}


	/**
	 * @param Node $node
	 * @param Scope $scope
	 * @param string $controlStructure
	 * @param list<DisallowedControlStructure> $disallowedControlStructures
	 * @param string $identifier
	 * @return list<RuleError>
	 * @throws ShouldNotHappenException
	 */
	public function get(Node $node, Scope $scope, string $controlStructure, array $disallowedControlStructures, string $identifier): array
	{
		foreach ($disallowedControlStructures as $disallowedControlStructure) {
			if (
				$disallowedControlStructure->getControlStructure() === $controlStructure
				&& !$this->allowed->isAllowed($node, $scope, null, $disallowedControlStructure)
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
