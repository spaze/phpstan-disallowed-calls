<?php
declare(strict_types = 1);

namespace Spaze\PHPStan\Rules\Disallowed\RuleErrors;

use PHPStan\Analyser\Scope;
use PHPStan\Rules\IdentifierRuleError;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\ShouldNotHappenException;
use Spaze\PHPStan\Rules\Disallowed\Allowed\AllowedPath;
use Spaze\PHPStan\Rules\Disallowed\DisallowedNamespace;
use Spaze\PHPStan\Rules\Disallowed\Formatter\Formatter;
use Spaze\PHPStan\Rules\Disallowed\Identifier\Identifier;

class DisallowedNamespaceRuleErrors
{

	public function __construct(
		private readonly AllowedPath $allowedPath,
		private readonly Identifier $identifier,
		private readonly Formatter $formatter,
	) {
	}


	/**
	 * @param list<DisallowedNamespace> $disallowedNamespaces
	 * @param string $identifier
	 * @return list<IdentifierRuleError>
	 * @throws ShouldNotHappenException
	 */
	public function getDisallowedMessage(string $namespace, string $description, Scope $scope, array $disallowedNamespaces, string $identifier): array
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
				$disallowedNamespace->getNamespace() !== $namespace ? " [{$namespace} matches {$disallowedNamespace->getNamespace()}]" : '',
			));
			$errorBuilder->identifier($disallowedNamespace->getErrorIdentifier() ?? $identifier);
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
