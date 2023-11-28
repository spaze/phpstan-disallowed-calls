<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\RuleErrors;

use PHPStan\Analyser\Scope;
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;
use Spaze\PHPStan\Rules\Disallowed\Allowed\AllowedPath;
use Spaze\PHPStan\Rules\Disallowed\DisallowedNamespace;
use Spaze\PHPStan\Rules\Disallowed\Formatter\Formatter;
use Spaze\PHPStan\Rules\Disallowed\Identifier\Identifier;

class DisallowedNamespaceRuleErrors
{

	/** @var AllowedPath */
	private $allowedPath;

	/** @var Identifier */
	private $identifier;

	/** @var Formatter */
	private $formatter;


	public function __construct(AllowedPath $allowedPath, Identifier $identifier, Formatter $formatter)
	{
		$this->allowedPath = $allowedPath;
		$this->identifier = $identifier;
		$this->formatter = $formatter;
	}


	/**
	 * @param string $namespace
	 * @param string $description
	 * @param Scope $scope
	 * @param list<DisallowedNamespace> $disallowedNamespaces
	 * @return list<RuleError>
	 */
	public function getDisallowedMessage(string $namespace, string $description, Scope $scope, array $disallowedNamespaces): array
	{
		foreach ($disallowedNamespaces as $disallowedNamespace) {
			if ($this->allowedPath->isAllowedPath($scope, $disallowedNamespace)) {
				continue;
			}

			if (!$this->identifier->matches($disallowedNamespace->getNamespace(), $namespace, $disallowedNamespace->getExcludes())) {
				continue;
			}

			$errorBuilder = RuleErrorBuilder::message(sprintf(
				'%s %s is forbidden%s%s',
				$description,
				$namespace,
				$this->formatter->formatDisallowedMessage($disallowedNamespace->getMessage()),
				$disallowedNamespace->getNamespace() !== $namespace ? " [{$namespace} matches {$disallowedNamespace->getNamespace()}]" : ''
			));
			if ($disallowedNamespace->getErrorIdentifier()) {
				$errorBuilder->identifier($disallowedNamespace->getErrorIdentifier());
			}
			if ($disallowedNamespace->getErrorTip()) {
				$errorBuilder->tip($disallowedNamespace->getErrorTip());
			}
			return [
				$errorBuilder->build(),
			];
		}

		return [];
	}

}
